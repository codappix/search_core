<?php
namespace Codappix\SearchCore\Domain\Search;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
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
use Codappix\SearchCore\Connection\SearchRequestInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use Codappix\SearchCore\DataProcessing\Service as DataProcessorService;
use Codappix\SearchCore\Domain\Model\FacetRequest;
use Codappix\SearchCore\Domain\Model\SearchResult;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Service to process a search request.
 */
class SearchService
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var DataProcessorService
     */
    protected $dataProcessorService;

    /**
     * @param ConnectionInterface $connection
     * @param ConfigurationContainerInterface $configuration
     * @param ObjectManagerInterface $objectManager
     * @param DataProcessorService $dataProcessorService
     */
    public function __construct(
        ConnectionInterface $connection,
        ConfigurationContainerInterface $configuration,
        ObjectManagerInterface $objectManager,
        DataProcessorService $dataProcessorService
    ) {
        $this->connection = $connection;
        $this->configuration = $configuration;
        $this->objectManager = $objectManager;
        $this->dataProcessorService = $dataProcessorService;
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $searchRequest)
    {
        $this->addSize($searchRequest);
        $this->addConfiguredFacets($searchRequest);
        $this->addConfiguredFilters($searchRequest);

        // Add connection to request to enable paginate widget support
        $searchRequest->setConnection($this->connection);
        $searchRequest->setSearchService($this);

        return $this->processResult($this->connection->search($searchRequest));
    }

    /**
     * Add configured size of search result items to request.
     *
     * @param SearchRequestInterface $searchRequest
     */
    protected function addSize(SearchRequestInterface $searchRequest)
    {
        $searchRequest->setLimit(
            $this->configuration->getIfExists('searching.size') ?: 10
        );
    }

    /**
     * Add facets from configuration to request.
     *
     * @param SearchRequestInterface $searchRequest
     */
    protected function addConfiguredFacets(SearchRequestInterface $searchRequest)
    {
        $facetsConfig = $this->configuration->getIfExists('searching.facets');
        if ($facetsConfig === null) {
            return;
        }

        foreach ($facetsConfig as $identifier => $facetConfig) {
            $searchRequest->addFacet($this->objectManager->get(
                FacetRequest::class,
                $identifier,
                $facetConfig
            ));
        }
    }

    /**
     * Add filters from configuration, e.g. flexform or TypoScript.
     *
     * @param SearchRequestInterface $searchRequest
     */
    protected function addConfiguredFilters(SearchRequestInterface $searchRequest)
    {
        try {
            $filter = $searchRequest->getFilter();

            ArrayUtility::mergeRecursiveWithOverrule(
                $filter,
                $this->configuration->get('searching.filter'),
                true,
                false
            );

            $searchRequest->setFilter($filter);
        } catch (InvalidArgumentException $e) {
            // Nothing todo, no filter configured.
        }
    }

    /**
     * Processes the result, e.g. applies configured data processing to result.
     */
    public function processResult(SearchResultInterface $searchResult) : SearchResultInterface
    {
        try {
            $newSearchResultItems = [];
            foreach ($this->configuration->get('searching.dataProcessing') as $configuration) {
                foreach ($searchResult as $resultItem) {
                    $newSearchResultItems[] = $this->dataProcessorService->executeDataProcessor(
                        $configuration,
                        $resultItem->getPlainData()
                    );
                }
            }

            return $this->objectManager->get(
                SearchResult::class,
                $searchResult,
                $newSearchResultItems
            );
        } catch (InvalidArgumentException $e) {
            return $searchResult;
        }
    }
}
