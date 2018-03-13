<?php
namespace Codappix\SearchCore\Domain\Index;

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
use Codappix\SearchCore\Connection\ConnectionInterface;

/**
 * Will index the given table using configuration from TCA.
 */
class TcaIndexer extends AbstractIndexer
{
    /**
     * @var TcaIndexer\TcaTableService
     */
    protected $tcaTableService;

    /**
     * @param TcaIndexer\TcaTableService $tcaTableService
     * @param ConnectionInterface $connection
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(
        TcaIndexer\TcaTableService $tcaTableService,
        ConnectionInterface $connection,
        ConfigurationContainerInterface $configuration
    ) {
        parent::__construct($connection, $configuration);
        $this->tcaTableService = $tcaTableService;
    }

    /**
     * @return array|null
     */
    protected function getRecords(int $offset, int $limit)
    {
        $records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            $this->tcaTableService->getFields(),
            $this->tcaTableService->getTableClause(),
            $this->tcaTableService->getWhereClause(),
            '',
            '',
            (int) $offset . ',' . (int) $limit
        );
        if ($records === null) {
            return null;
        }

        $this->tcaTableService->filterRecordsByRootLineBlacklist($records);
        foreach ($records as &$record) {
            $this->tcaTableService->prepareRecord($record);
        }

        return $records;
    }

    /**
     * @throws NoRecordFoundException If record could not be found.
     */
    protected function getRecord(int $identifier) : array
    {
        $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
            $this->tcaTableService->getFields(),
            $this->tcaTableService->getTableClause(),
            $this->tcaTableService->getWhereClause()
                . ' AND ' . $this->tcaTableService->getTableName() . '.uid = ' . (int) $identifier
        );

        if ($record === false || $record === null) {
            throw new NoRecordFoundException(
                'Record could not be fetched from database: "' . $identifier . '". Perhaps record is not active.',
                1484225364
            );
        }
        $this->tcaTableService->prepareRecord($record);

        return $record;
    }

    protected function getDocumentName() : string
    {
        return $this->tcaTableService->getTableName();
    }
}
