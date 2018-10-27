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
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     */
    public function create(SearchRequestInterface $searchRequest): \Elastica\Query
    {
        return $this->createElasticaQuery($searchRequest);
    }

    protected function createElasticaQuery(SearchRequestInterface $searchRequest): \Elastica\Query
    {
        $query = [];
        $this->addSize($searchRequest, $query);
        $this->addSearch($searchRequest, $query);
        $this->addBoosts($searchRequest, $query);
        $this->addFilters($searchRequest, $query);
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
        ArrayUtility::mergeRecursiveWithOverrule($query, [
            'from' => $searchRequest->getOffset(),
            'size' => $searchRequest->getLimit(),
        ]);
    }

    protected function addSearch(SearchRequestInterface $searchRequest, array &$query)
    {
        if (trim($searchRequest->getSearchTerm()) === '') {
            return;
        }

        $matchExpression = [
            'type' => 'most_fields',
            'query' => $searchRequest->getSearchTerm(),
        ];

        try {
            $fieldsToQuery = GeneralUtility::trimExplode(
                ',',
                $this->configuration->get('searching.fields.query'),
                true
            );
            if ($fieldsToQuery !== []) {
                $matchExpression['fields'] = $fieldsToQuery;
            }
        } catch (InvalidArgumentException $e) {
            // Nothing configured
        }

        $minimumShouldMatch = $this->configuration->getIfExists('searching.minimumShouldMatch');
        if ($minimumShouldMatch) {
            $matchExpression['minimum_should_match'] = $minimumShouldMatch;
        }

        $query = ArrayUtility::setValueByPath($query, 'query.bool.must.0.multi_match', $matchExpression, '.');
    }

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

    protected function addSort(SearchRequestInterface $searchRequest, array &$query)
    {
        $sorting = $this->configuration->getIfExists('searching.sort') ?: [];
        $sorting = $this->configurationUtility->replaceArrayValuesWithRequestContent($searchRequest, $sorting);
        $sorting = $this->configurationUtility->filterByCondition($sorting);
        if ($sorting !== []) {
            ArrayUtility::mergeRecursiveWithOverrule($query, ['sort' => $sorting]);
        }
    }

    protected function addFilters(SearchRequestInterface $searchRequest, array &$query)
    {
        if (!$searchRequest->hasFilter()) {
            return;
        }

        foreach ($searchRequest->getFilter() as $name => $value) {
            $this->addFilter(
                $name,
                $value,
                $this->configuration->getIfExists('searching.mapping.filter.' . $name) ?: [],
                $query
            );
        }
    }

    protected function addFilter(string $name, $value, array $config, array &$query): array
    {
        if (!empty($config)) {
            if ($config['type'] && $config['type'] === 'user') {
                if (!isset($config['userFunc'])) {
                    throw new MissingAttributeException('No userFunc configured for filter type: user', 1539876182018);
                }

                $parameters = [
                    'config' => $config,
                    'value' => $value,
                    'query' => &$query,
                ];
                GeneralUtility::callUserFunction($config['userFunc'], $parameters, $this);
            } else {
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
                    $query['query']['bool']['filter'][] = [
                        'range' => [
                            $config['field'] => $filter
                        ]
                    ];
                } else {
                    $query['query']['bool']['filter'][] = [
                        $config['field'] => $filter
                    ];
                }
            }
        } else {
            $query['query']['bool']['filter'][] = [
                'term' => [
                    $name => $value
                ]
            ];
        }

        return $query;
    }

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
