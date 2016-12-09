<?php
namespace Leonmrni\SearchCore\Domain\Index;

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

use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use Leonmrni\SearchCore\Connection\ConnectionInterface;

/**
 * Will index the given table using configuration from TCA.
 */
class TcaIndexer implements IndexerInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var TcaIndexer\TcaTableService
     */
    protected $tcaTableService;

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
     * @param string $tableName
     * @param ConnectionInterface $connection
     */
    public function __construct(
        TcaIndexer\TcaTableService $tcaTableService,
        ConnectionInterface $connection
    ) {
        $this->tcaTableService = $tcaTableService;
        $this->connection = $connection;
    }

    public function index()
    {
        $this->logger->info('Start indexing');
        foreach ($this->getRecordGenerator() as $records) {
            $this->logger->debug('Index records.', [$records]);
            $this->connection->addDocuments($this->tcaTableService->getTableName(), $records);
        }
        $this->logger->info('Finish indexing');
    }

    /**
     * @return \Iterator
     */
    protected function getRecordGenerator()
    {
        $offset = 0;
        // TODO: Make configurable.
        $limit = 50;

        while (($records = $this->getRecords($offset, $limit)) !== []) {
            yield $records;
            $offset += $limit;
        }
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function getRecords($offset, $limit)
    {
        $records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            $this->tcaTableService->getFields(),
            $this->tcaTableService->getTableName(),
            $this->tcaTableService->getWhereClause(),
            '',
            '',
            (int) $offset . ',' . (int) $limit
        );

        foreach ($records as &$record) {
            $this->tcaTableService->prepareRecord($record);
        }

        // TODO: Ignore records from sys folder?

        return $records;
    }
}
