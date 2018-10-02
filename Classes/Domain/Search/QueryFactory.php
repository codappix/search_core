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
use Codappix\SearchCore\Configuration\ConfigurationUtility;
use Codappix\SearchCore\Configuration\InvalidArgumentException;
use Codappix\SearchCore\Connection\SearchRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

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
     * @var ConfigurationUtility
     */
    protected $configurationUtility;

    /**
     * QueryFactory constructor.
     * @param \TYPO3\CMS\Core\Log\LogManager $logManager
     * @param ConfigurationContainerInterface $configuration
     * @param ConfigurationUtility $configurationUtility
     */
    public function __construct(
        \TYPO3\CMS\Core\Log\LogManager $logManager,
        ConfigurationContainerInterface $configuration,
        ConfigurationUtility $configurationUtility
    ) {
        $this->logger = $logManager->getLogger(__CLASS__);
        $this->configuration = $configuration;
        $this->configurationUtility = $configurationUtility;
    }

    /**
     * TODO: This is not in scope Elasticsearch, therefore it should not return
     * \Elastica\Query, but decide to use a more specific QueryFactory like
     * ElasticaQueryFactory, once the second query is added?
     *
     * @param SearchRequestInterface $searchRequest
     * @return \Elastica\Query
     */
    public function create(SearchRequestInterface $searchRequest): \Elastica\Query
    {
        return $this->createElasticaQuery($searchRequest);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return \Elastica\Query
     */
    protected function createElasticaQuery(SearchRequestInterface $searchRequest): \Elastica\Query
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

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array $query
     */
    protected function addSize(SearchRequestInterface $searchRequest, array &$query)
    {
        ArrayUtility::mergeRecursiveWithOverrule($query, [
            'from' => $searchRequest->getOffset(),
            'size' => $searchRequest->getLimit(),
        ]);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array $query
     */
    protected function addSearch(SearchRequestInterface $searchRequest, array &$query)
    {
        if (trim($searchRequest->getSearchTerm()) === '') {
            return;
        }

        $matchExpression = [
            'type' => 'most_fields',
            'query' => $searchRequest->getSearchTerm(),
            'fields' => GeneralUtility::trimExplode(',', $this->configuration->get('searching.fields.query')),
        ];

        $minimumShouldMatch = $this->configuration->getIfExists('searching.minimumShouldMatch');
        if ($minimumShouldMatch) {
            $matchExpression['minimum_should_match'] = $minimumShouldMatch;
        }

        $query = ArrayUtility::setValueByPath($query, 'query.bool.must.0.multi_match', $matchExpression, '.');
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array $query
     */
    protected function addBoosts(SearchRequestInterface $searchRequest, array &$query)
    {
        try {
            $fields = $this->configuration->get('searching.boost');
        } catch (InvalidArgumentException $e) {
            return;
        }

        if (trim($searchRequest->getSearchTerm()) === '') {
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

        if (!empty($boostQueryParts)) {
            ArrayUtility::mergeRecursiveWithOverrule($query, [
                'query' => [
                    'bool' => [
                        'should' => $boostQueryParts,
                    ],
                ],
            ]);
        }
    }

    /**
     * @param array $query
     * @return void
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
     * @param array $query
     * @return void
     */
    protected function addFields(SearchRequestInterface $searchRequest, array &$query)
    {
        try {
            ArrayUtility::mergeRecursiveWithOverrule($query, [
                'stored_fields' => GeneralUtility::trimExplode(
                    ',',
                    $this->configuration->get('searching.fields.stored_fields'),
                    true
                ),
            ]);
        } catch (InvalidArgumentException $e) {
            // Nothing configured
        }

        try {
            $scriptFields = $this->configuration->get('searching.fields.script_fields');
            $scriptFields = $this->configurationUtility->replaceArrayValuesWithRequestContent(
                $searchRequest,
                $scriptFields
            );
            $scriptFields = $this->configurationUtility->filterByCondition($scriptFields);
            if ($scriptFields !== []) {
                ArrayUtility::mergeRecursiveWithOverrule($query, ['script_fields' => $scriptFields]);
            }
        } catch (InvalidArgumentException $e) {
            // Nothing configured
        }
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array $query
     * @return void
     */
    protected function addSort(SearchRequestInterface $searchRequest, array &$query)
    {
        $sorting = $this->configuration->getIfExists('searching.sort') ?: [];
        $sorting = $this->configurationUtility->replaceArrayValuesWithRequestContent($searchRequest, $sorting);
        $sorting = $this->configurationUtility->filterByCondition($sorting);
        if ($sorting !== []) {
            ArrayUtility::mergeRecursiveWithOverrule($query, ['sort' => $sorting]);
        }
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array $query
     * @return void
     */
    protected function addFilter(SearchRequestInterface $searchRequest, array &$query)
    {
        if (!$searchRequest->hasFilter()) {
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

        ArrayUtility::mergeRecursiveWithOverrule($query, [
            'query' => [
                'bool' => [
                    'filter' => $filter,
                ],
            ],
        ]);
    }

    /**
     * @param string $name
     * @param $value
     * @param array $config
     * @return array
     */
    protected function buildFilter(string $name, $value, array $config): array
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

        if (isset($config['raw'])) {
            $filter = array_merge($config['raw'], $filter);
        }

        if ($config['type'] === 'range') {
            return [
                'range' => [
                    $config['field'] => $filter,
                ],
            ];
        }

        return [$config['field'] => $filter];
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @param array $query
     * @return void
     */
    protected function addFacets(SearchRequestInterface $searchRequest, array &$query)
    {
        foreach ($searchRequest->getFacets() as $facet) {
            ArrayUtility::mergeRecursiveWithOverrule($query, [
                'aggs' => [
                    $facet->getIdentifier() => $facet->getConfig(),
                ],
            ]);
        }
    }
}
