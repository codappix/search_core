<?php
namespace Leonmrni\SearchCore\Connection;

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

use TYPO3\CMS\Core\SingletonInterface as Singleton;

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
     * @var Elasticsearch\DocumentFactory
     */
    protected $documentFactory;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

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
     * @param Elasticsearch\Connection $connection
     * @param Elasticsearch\IndexFactory $indexFactory
     * @param Elasticsearch\TypeFactory $typeFactory
     * @param Elasticsearch\DocumentFactory $documentFactory
     */
    public function __construct(
        Elasticsearch\Connection $connection,
        Elasticsearch\IndexFactory $indexFactory,
        Elasticsearch\TypeFactory $typeFactory,
        Elasticsearch\DocumentFactory $documentFactory
    ) {
        $this->connection = $connection;
        $this->indexFactory = $indexFactory;
        $this->typeFactory = $typeFactory;
        $this->documentFactory = $documentFactory;
    }

    public function add($documentType, array $document)
    {
         throw new \Exception('Implement', 1481190734);
    }

    public function delete($documentType, $identifier)
    {
         throw new \Exception('Implement', 1481190734);
    }

    public function update($documentType, array $document)
    {
         throw new \Exception('Implement', 1481190734);
    }

    /**
     * Add the given documents to elasticsearch.
     *
     * @param string $documentType
     * @param array $documents
     */
    public function addDocuments($documentType, array $documents)
    {
        $type = $this->typeFactory->getType(
            $this->indexFactory->getIndex(
                $this->connection,
                $documentType
            ),
            $documentType
        );

        $type->addDocuments(
            $this->documentFactory->getDocuments($documentType, $documents)
        );

        $type->getIndex()->refresh();
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $searchRequest)
    {
        $this->logger->debug('Search for', [$searchRequest->getSearchTerm()]);

        $search = new \Elastica\Search($this->connection->getClient());
        $search->addIndex('typo3content');

        // TODO: Return wrapped result to implement our interface.
        return $search->search($searchRequest->getSearchTerm());
    }
}
