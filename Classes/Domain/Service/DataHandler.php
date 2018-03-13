<?php
namespace Codappix\SearchCore\Domain\Service;

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
use Codappix\SearchCore\Domain\Index\IndexerFactory;
use Codappix\SearchCore\Domain\Index\IndexerInterface;
use Codappix\SearchCore\Domain\Index\NoMatchingIndexerException;
use TYPO3\CMS\Core\SingletonInterface as Singleton;

/**
 * Handles all data related things like updates, deletes and inserts.
 *
 * This is the place to add mappings of further parts to adjust the data before
 * sending ot to connection.
 *
 * TODO: Probably a candidate for deletion. Currently this class makes use of
 * extbase DI. We have to resolve this somehow.
 *
 * I think we keep it for easier testing and DI.
 */
class DataHandler implements Singleton
{
    /**
     * TODO: Only inject on first use?!
     *
     * @var \Codappix\SearchCore\Connection\ConnectionInterface
     * @inject
     */
    protected $connection;

    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

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
     * @param ConfigurationContainerInterface $configuration
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(ConfigurationContainerInterface $configuration, IndexerFactory $indexerFactory)
    {
        $this->configuration = $configuration;
        $this->indexerFactory = $indexerFactory;
    }

    public function update(string $table, array $record)
    {
        $this->logger->debug('Record received for update.', [$table, $record]);
        $this->getIndexer($table)->indexDocument($record['uid']);
    }

    public function delete(string $table, string $identifier)
    {
        $this->logger->debug('Record received for delete.', [$table, $identifier]);
        $this->connection->deleteDocument($table, $identifier);
    }

    /**
     * @throws NoMatchingIndexerException
     */
    protected function getIndexer(string $table) : IndexerInterface
    {
        return $this->indexerFactory->getIndexer($table);
    }

    public function supportsTable(string $table) : bool
    {
        try {
            $this->getIndexer($table);
            return true;
        } catch (NoMatchingIndexerException $e) {
            return false;
        }
    }
}
