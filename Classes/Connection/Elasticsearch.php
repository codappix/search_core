<?php

namespace Codappix\SearchCore\Connection;

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

use Codappix\SearchCore\Connection\Elasticsearch\SearchResult;
use Codappix\SearchCore\Domain\Search\QueryFactory;
use Elastica\Query;
use Elastica\Type;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Outer wrapper to elasticsearch.
 */
class Elasticsearch implements Singleton, ConnectionInterface
{
    /**
     * @var Elasticsearch\Connection
     */
    protected $connection;

    /**
     * @var Elasticsearch\IndexFactory
     */
    protected $indexFactory;

    /**
     * @var Elasticsearch\TypeFactory
     */
    protected $typeFactory;

    /**
     * @var Elasticsearch\MappingFactory
     */
    protected $mappingFactory;

    /**
     * @var Elasticsearch\DocumentFactory
     */
    protected $documentFactory;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Inject log manager to get concrete logger from it.
     *
     * @param \TYPO3\CMS\Core\Log\LogManager $logManager
     */
    public function injectLogger(\TYPO3\CMS\Core\Log\LogManager $logManager)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
    }

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Elasticsearch\Connection $connection
     * @param Elasticsearch\IndexFactory $indexFactory
     * @param Elasticsearch\TypeFactory $typeFactory
     * @param Elasticsearch\MappingFactory $mappingFactory
     * @param Elasticsearch\DocumentFactory $documentFactory
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        Elasticsearch\Connection $connection,
        Elasticsearch\IndexFactory $indexFactory,
        Elasticsearch\TypeFactory $typeFactory,
        Elasticsearch\MappingFactory $mappingFactory,
        Elasticsearch\DocumentFactory $documentFactory,
        QueryFactory $queryFactory
    ) {
        $this->connection = $connection;
        $this->indexFactory = $indexFactory;
        $this->typeFactory = $typeFactory;
        $this->mappingFactory = $mappingFactory;
        $this->documentFactory = $documentFactory;
        $this->queryFactory = $queryFactory;
    }

    public function addDocument(string $documentType, array $document)
    {
        $this->withType(
            $documentType,
            function (Type $type, string $documentType) use ($document) {
                $type->addDocument($this->documentFactory->getDocument($documentType, $document));
            }
        );
    }

    public function deleteDocument(string $documentType, string $identifier)
    {
        try {
            $this->withType(
                $documentType,
                function (Type $type, string $documentType) use ($identifier) {
                    $type->deleteById($identifier);
                }
            );
        } catch (\Elastica\Exception\NotFoundException $exception) {
            $this->logger->debug(
                'Tried to delete document in index, which does not exist.',
                [$documentType, $identifier]
            );
        }
    }

    public function deleteAllDocuments(string $documentType)
    {
        $this->deleteDocumentsByQuery($documentType, Query::create([
            'query' => [
                'term' => [
                    'search_document_type' => $documentType,
                ],
            ],
        ]));
    }

    public function deleteIndex(string $documentType)
    {
        try {
            $this->indexFactory->getIndex($this->connection, $documentType)->delete();
        } catch (\InvalidArgumentException $e) {
            $this->logger->notice(
                'Index did not exist, therefore was not deleted.',
                [$documentType, $e]
            );
        }
    }

    public function updateDocument(string $documentType, array $document)
    {
        $this->withType(
            $documentType,
            function (Type $type, string $documentType) use ($document) {
                $type->updateDocument($this->documentFactory->getDocument($documentType, $document));
            }
        );
    }

    public function addDocuments(string $documentType, array $documents)
    {
        $this->withType(
            $documentType,
            function (Type $type, string $documentType) use ($documents) {
                $type->addDocuments($this->documentFactory->getDocuments($documentType, $documents));
            }
        );
    }

    public function search(SearchRequestInterface $searchRequest): SearchResultInterface
    {
        $this->logger->debug('Search for', [$searchRequest->getSearchTerm()]);

        $search = new \Elastica\Search($this->connection->getClient());
        $search->addIndex($this->indexFactory->getIndexName());
        $search->setQuery($this->queryFactory->create($searchRequest));

        return $this->objectManager->get(SearchResult::class, $searchRequest, $search->search());
    }

    /**
     * Execute given callback with Elastica Type based on provided documentType
     */
    private function withType(string $documentType, callable $callback)
    {
        $type = $this->typeFactory->getType($documentType);
        // TODO: Check whether it's to heavy to send it so often e.g. for every single document.
        // Perhaps add command controller to submit mapping?!
        // Also it's not possible to change mapping without deleting index first.
        // Mattes told about a solution.
        // So command looks like the best way so far, except we manage mattes solution.
        // Still then this should be done once. So perhaps singleton which tracks state and does only once?
        $this->mappingFactory->getMapping($documentType)->send();
        $callback($type, $documentType);
        $type->getIndex()->refresh();
    }

    private function deleteDocumentsByQuery(string $documentType, Query $query)
    {
        try {
            $index = $this->indexFactory->getIndex($this->connection, $documentType);
            $response = $index->deleteByQuery($query);

            if ($response->getData()['deleted'] > 0) {
                // Refresh index when delete query is invoked
                $index->refresh();
            }
        } catch (\InvalidArgumentException $e) {
            $this->logger->notice(
                'Index did not exist, therefore items can not be deleted by query.',
                [$documentType, $query->getQuery()]
            );
        }
    }
}
