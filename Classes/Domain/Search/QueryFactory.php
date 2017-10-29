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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

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
     */
    public function create(SearchRequestInterface $searchRequest) : \Elastica\Query
    {
        return $this->createElasticaQuery($searchRequest);
    }

    protected function createElasticaQuery(SearchRequestInterface $searchRequest) : \Elastica\Query
    {
        $query = [];
        $this->addSize($searchRequest, $query);
        $this->addSearch($searchRequest, $query);
        $this->addBoosts($searchRequest, $query);
        $this->addFilter($searchRequest, $query);
        $this->addFacets($searchRequest, $query);
        $this->addFields($searchRequest, $query);
        $this->addSort($searchRequest, $query);

        // Use last, as it might change structure of query.
        // Better approach would be something like DQL to generate query and build result in the end.
        $this->addFactorBoost($query);

        $this->logger->debug('Generated elasticsearch query.', [$query]);
        return new \Elastica\Query($query);
    }

    protected function addSize(SearchRequestInterface $searchRequest, array &$query)
    {
        $query = ArrayUtility::arrayMergeRecursiveOverrule($query, [
            'from' => $searchRequest->getOffset(),
            'size' => $searchRequest->getLimit(),
        ]);
    }

    protected function addSearch(SearchRequestInterface $searchRequest, array &$query)
    {
        if (trim($searchRequest->getSearchTerm()) === '') {
            return;
        }

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

    protected function addFields(SearchRequestInterface $searchRequest, array &$query)
    {
        try {
            $query = ArrayUtility::arrayMergeRecursiveOverrule($query, [
                'stored_fields' => GeneralUtility::trimExplode(',', $this->configuration->get('searching.fields.stored_fields'), true),
            ]);
        } catch (InvalidArgumentException $e) {
            // Nothing configured
        }

        try {
            $scriptFields = $this->configuration->get('searching.fields.script_fields');
            $scriptFields = $this->replaceArrayValuesWithRequestContent($searchRequest, $scriptFields);
            $scriptFields = $this->filterByCondition($scriptFields);
            if ($scriptFields !== []) {
                $query = ArrayUtility::arrayMergeRecursiveOverrule($query, ['script_fields' => $scriptFields]);
            }
        } catch (InvalidArgumentException $e) {
            // Nothing configured
        }
    }

    protected function addSort(SearchRequestInterface $searchRequest, array &$query)
    {
        $sorting = $this->configuration->getIfExists('searching.sort') ?: [];
        $sorting = $this->replaceArrayValuesWithRequestContent($searchRequest, $sorting);
        $sorting = $this->filterByCondition($sorting);
        if ($sorting !== []) {
            $query = ArrayUtility::arrayMergeRecursiveOverrule($query, ['sort' => $sorting]);
        }
    }

    protected function addFilter(SearchRequestInterface $searchRequest, array &$query)
    {
        if (! $searchRequest->hasFilter()) {
            return;
        }

        $filter = [];
        foreach ($searchRequest->getFilter() as $name => $value) {
            $filter[] = $this->buildFilter(
                $name,
                $value,
                $this->configuration->getIfExists('searching.mapping.filter.' . $name) ?: []
            );
        }

        $query = ArrayUtility::arrayMergeRecursiveOverrule($query, [
            'query' => [
                'bool' => [
                    'filter' => $filter,
                ],
            ],
        ]);
    }

    protected function buildFilter(string $name, $value, array $config) : array
    {
        if ($config === []) {
            return [
                'term' => [
                    $name => $value,
                ],
            ];
        }

        $filter = [];

        if (isset($config['fields'])) {
            foreach ($config['fields'] as $elasticField => $inputField) {
                $filter[$elasticField] = $value[$inputField];
            }
        }

        return [$config['field'] => $filter];
    }

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

    protected function replaceArrayValuesWithRequestContent(SearchRequestInterface $searchRequest, array $array) : array
    {
        array_walk_recursive($array, function (&$value, $key, SearchRequestInterface $searchRequest) {
            $template = new StandaloneView();
            $template->assign('request', $searchRequest);
            $template->setTemplateSource($value);
            $value = $template->render();

            // As elasticsearch does need some doubles to be end as doubles.
            if (is_numeric($value)) {
                $value = (float) $value;
            }
        }, $searchRequest);

        return $array;
    }

    protected function filterByCondition(array $entries) : array
    {
        $entries = array_filter($entries, function ($entry) {
            return !array_key_exists('condition', $entry) || (bool) $entry['condition'] === true;
        });

        foreach ($entries as $key => $entry) {
            if (array_key_exists('condition', $entry)) {
                unset($entries[$key]['condition']);
            }
        }

        return $entries;
    }
}
