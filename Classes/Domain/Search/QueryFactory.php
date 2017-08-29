<?php
namespace Codappix\SearchCore\Domain\Search;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Configuration\InvalidArgumentException;
use Codappix\SearchCore\Connection\ConnectionInterface;
use Codappix\SearchCore\Connection\Elasticsearch\Query;
use Codappix\SearchCore\Connection\SearchRequestInterface;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;

class QueryFactory
{
    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @param \TYPO3\CMS\Core\Log\LogManager $logManager
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(
        \TYPO3\CMS\Core\Log\LogManager $logManager,
        ConfigurationContainerInterface $configuration
    ) {
        $this->logger = $logManager->getLogger(__CLASS__);
        $this->configuration = $configuration;
    }

    /**
     * TODO: This is not in scope Elasticsearch, therefore it should not return
     * \Elastica\Query, but decide to use a more specific QueryFactory like
     * ElasticaQueryFactory, once the second query is added?
     *
     * @param SearchRequestInterface $searchRequest
     *
     * @return \Elastica\Query
     */
    public function create(SearchRequestInterface $searchRequest)
    {
        return $this->createElasticaQuery($searchRequest);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     *
     * @return \Elastica\Query
     */
    protected function createElasticaQuery(SearchRequestInterface $searchRequest)
    {
        $query = [];
        $this->addSize($searchRequest, $query);
        $this->addSearch($searchRequest, $query);
        $this->addBoosts($searchRequest, $query);
        $this->addFilter($searchRequest, $query);
        $this->addFacets($searchRequest, $query);

        // Use last, as it might change structure of query.
        // Better approach would be something like DQL to generate query and build result in the end.
        $this->addFactorBoost($query);

        $this->logger->debug('Generated elasticsearch query.', [$query]);
        $elasticaQuery = new \Elastica\Query($query);
        $this->addSuggest($elasticaQuery, $searchRequest);
        return $elasticaQuery;
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array &$query
     */
    protected function addSize(SearchRequestInterface $searchRequest, array &$query)
    {
        $query = ArrayUtility::arrayMergeRecursiveOverrule($query, [
            'from' => $searchRequest->getOffset(),
            'size' => $searchRequest->getLimit(),
        ]);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array &$query
     */
    protected function addSearch(SearchRequestInterface $searchRequest, array &$query)
    {
        $query = ArrayUtility::setValueByPath(
            $query,
            'query.bool.must.0.match._all.query',
            $searchRequest->getSearchTerm()
        );

        $minimumShouldMatch = $this->configuration->getIfExists('searching.minimumShouldMatch');
        if ($minimumShouldMatch) {
            $query = ArrayUtility::setValueByPath(
                $query,
                'query.bool.must.0.match._all.minimum_should_match',
                $minimumShouldMatch
            );
        }
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array &$query
     */
    protected function addBoosts(SearchRequestInterface $searchRequest, array &$query)
    {
        try {
            $fields = $this->configuration->get('searching.boost');
        } catch (InvalidArgumentException $e) {
            return;
        }

        $boostQueryParts = [];

        foreach ($fields as $fieldName => $boostValue) {
            $boostQueryParts[] = [
                'match' => [
                    $fieldName => [
                        'query' => $searchRequest->getSearchTerm(),
                        'boost' => $boostValue,
                    ],
                ],
            ];
        }

        $query = ArrayUtility::arrayMergeRecursiveOverrule($query, [
            'query' => [
                'bool' => [
                    'should' => $boostQueryParts,
                ],
            ],
        ]);
    }

    /**
     * @param array &$query
     */
    protected function addFactorBoost(array &$query)
    {
        try {
            $query['query'] = [
                'function_score' => [
                    'query' => $query['query'],
                    'field_value_factor' => $this->configuration->get('searching.fieldValueFactor'),
                ],
            ];
        } catch (InvalidArgumentException $e) {
            return;
        }
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array &$query
     */
    protected function addFilter(SearchRequestInterface $searchRequest, array &$query)
    {
        if (! $searchRequest->hasFilter()) {
            return;
        }

        $terms = [];
        foreach ($searchRequest->getFilter() as $name => $value) {
            $terms[] = [
                'term' => [
                    $name => $value,
                ],
            ];
        }

        $query = ArrayUtility::arrayMergeRecursiveOverrule($query, [
            'query' => [
                'bool' => [
                    'filter' => $terms,
                ],
            ],
        ]);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array &$query
     */
    protected function addFacets(SearchRequestInterface $searchRequest, array &$query)
    {
        foreach ($searchRequest->getFacets() as $facet) {
            $query = ArrayUtility::arrayMergeRecursiveOverrule($query, [
                'aggs' => [
                    $facet->getIdentifier() => [
                        'terms' => [
                            'field' => $facet->getField(),
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * @param \Elastica\Query $query
     * @param SearchRequestInterface $searchRequest
     */
    protected function addSuggest(\Elastica\Query $query, SearchRequestInterface $searchRequest)
    {
        $suggests = [];
        $suggests[] = $this->getSpellcheck($searchRequest);
        $suggests = array_filter($suggests);

        if (!$suggests) {
            return;
        }

        $suggest = new \Elastica\Suggest();
        foreach ($suggests as $suggestToAdd) {
            $suggest->addSuggestion($suggestToAdd);
        }
        $query->setSuggest($suggest);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return \Elastica\Suggest\Phrase|null
     */
    protected function getSpellcheck(SearchRequestInterface $searchRequest)
    {
        try {
            $suggest = new \Elastica\Suggest\Phrase(
                'spellcheck',
                $this->configuration->get('searching.spellcheck.field')
            );
            $suggest->setText($searchRequest->getSearchTerm());
            $suggest->setSize($this->configuration->getIfExists('searching.spellcheck.size') ?: 5);

            return $suggest;
        } catch (InvalidArgumentException $e) {
            return;
        }
    }
}
