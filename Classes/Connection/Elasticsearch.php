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

    public function addDocument($documentType, array $document)
    {
        $this->withType(
            $documentType,
            function ($type) use ($document) {
                $type->addDocument($this->documentFactory->getDocument($type->getName(), $document));
            }
        );
    }

    public function deleteDocument($documentType, $identifier)
    {
        try {
            $this->withType(
                $documentType,
                function ($type) use ($identifier) {
                    $type->deleteById($identifier);
                }
            );
        } catch (\Elastica\Exception\NotFoundException $exception) {
            $this->logger->debug('Tried to delete document in index, which does not exist.', [$documentType, $identifier]);
        }
    }

    public function updateDocument($documentType, array $document)
    {
        $this->withType(
            $documentType,
            function ($type) use ($document) {
                $type->updateDocument($this->documentFactory->getDocument($type->getName(), $document));
            }
        );
    }

    public function addDocuments($documentType, array $documents)
    {
        $this->withType(
            $documentType,
            function ($type) use ($documents) {
                $type->addDocuments($this->documentFactory->getDocuments($type->getName(), $documents));
            }
        );
    }

    public function deleteIndex($documentType)
    {
        $index = $this->connection->getClient()->getIndex($this->indexFactory->getIndexName());

        if (! $index->exists()) {
            $this->logger->notice(
                'Index did not exist, therefore was not deleted.',
                [$documentType, $this->indexFactory->getIndexName()]
            );
            return;
        }

        $index->delete();
    }

    /**
     * Execute given callback with Elastica Type based on provided documentType
     *
     * @param string $documentType
     * @param callable $callback
     */
    protected function withType($documentType, callable $callback)
    {
        $type = $this->getType($documentType);
        // TODO: Check whether it's to heavy to send it so often e.g. for every single document.
        // Perhaps add command controller to submit mapping?!
        // Also it's not possible to change mapping without deleting index first.
        // Mattes told about a solution.
        // So command looks like the best way so far, except we manage mattes solution.
        // Still then this should be done once. So perhaps singleton which tracks state and does only once?
        $this->mappingFactory->getMapping($type)->send();
        $callback($type);
        $type->getIndex()->refresh();
    }

    /**
     * @param SearchRequestInterface $searchRequest
     *
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $searchRequest)
    {
        $this->logger->debug('Search for', [$searchRequest->getSearchTerm()]);

        $search = new \Elastica\Search($this->connection->getClient());
        $search->addIndex($this->indexFactory->getIndexName());
        $search->setQuery($this->queryFactory->create($searchRequest));

        return $this->objectManager->get(SearchResult::class, $searchRequest, $search->search());
    }

    /**
     * @param string $documentType
     *
     * @return \Elastica\Type
     */
    protected function getType($documentType)
    {
        return $this->typeFactory->getType(
            $this->indexFactory->getIndex(
                $this->connection,
                $documentType
            ),
            $documentType
        );
    }
}
