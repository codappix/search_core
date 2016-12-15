<?php
namespace Leonmrni\SearchCore\Domain\Service;

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

use Leonmrni\SearchCore\Configuration\ConfigurationContainerInterface;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @var \Leonmrni\SearchCore\Connection\ConnectionInterface
     * @inject
     */
    protected $connection;

    /**
     * @var \Leonmrni\SearchCore\Domain\Index\IndexerFactory
     * @inject
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
     */
    public function __construct(ConfigurationContainerInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get all tables that are allowed for indexing.
     *
     * @return array<String>
     */
    public function getAllowedTablesForIndexing()
    {
        return GeneralUtility::trimExplode(',', $this->configuration->get('index', 'allowedTables'));
    }

    /**
     * @param string $table
     * @param array $record
     */
    public function add($table, array $record)
    {
        $this->logger->debug('Record received for add.', [$table, $record]);
        $this->indexerFactory->getIndexer($table)->indexDocument($record['uid']);
    }

    /**
     * @param string $table
     */
    public function update($table, array $record)
    {
        $this->logger->debug('Record received for update.', [$table, $record]);
        $this->indexerFactory->getIndexer($table)->indexDocument($record['uid']);
    }

    /**
     * @param string $table
     * @param int $identifier
     */
    public function delete($table, $identifier)
    {
        $this->logger->debug('Record received for delete.', [$table, $identifier]);
        $this->connection->deleteDocument($table, $identifier);
    }
}
