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
use Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableServiceInterface;

/**
 * Will index the given table using configuration from TCA.
 */
class TcaIndexer extends AbstractIndexer
{
    /**
     * @var TcaTableServiceInterface
     */
    protected $tcaTableService;

    /**
     * @param TcaTableServiceInterface $tcaTableService
     * @param ConnectionInterface $connection
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(
        TcaTableServiceInterface $tcaTableService,
        ConnectionInterface $connection,
        ConfigurationContainerInterface $configuration
    ) {
        parent::__construct($connection, $configuration);
        $this->tcaTableService = $tcaTableService;
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return array|null
     */
    protected function getRecords(int $offset, int $limit)
    {
        $records = $this->tcaTableService->getRecords($offset, $limit);
        if ($records === []) {
            return null;
        }

        $this->tcaTableService->filterRecordsByRootLineBlacklist($records);
        foreach ($records as &$record) {
            $this->tcaTableService->prepareRecord($record);
        }

        return $records;
    }

    /**
     * @param integer $identifier
     * @return array
     * @throws NoRecordFoundException If record could not be found.
     */
    protected function getRecord(int $identifier): array
    {
        $record = $this->tcaTableService->getRecord($identifier);
        if ($record === []) {
            throw new NoRecordFoundException(
                'Record could not be fetched from database: "' . $identifier . '". Perhaps record is not active.',
                1484225364
            );
        }
        $this->tcaTableService->prepareRecord($record);

        return $record;
    }

    /**
     * @return string
     */
    protected function getDocumentName(): string
    {
        return $this->tcaTableService->getTableName();
    }

    /**
     * @param string $identifier
     * @return string
     */
    public function getDocumentIdentifier($identifier): string
    {
        return $this->getDocumentName() . '-' . $identifier;
    }
}
