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

use Codappix\SearchCore\Connection\ConnectionInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     */
    public function __construct(
        TcaIndexer\TcaTableService $tcaTableService,
        ConnectionInterface $connection
    ) {
        $this->tcaTableService = $tcaTableService;
        $this->connection = $connection;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array|null
     */
    protected function getRecords($offset, $limit)
    {
        $records = $this->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAll();

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
     * @param int $identifier
     * @return array
     * @throws NoRecordFoundException If record could not be found.
     */
    protected function getRecord($identifier)
    {
        $record = $this->getQuery()->execute()->fetch();

        if ($record === false || $record === null) {
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
    protected function getDocumentName()
    {
        return $this->tcaTableService->getTableName();
    }

    protected function getQuery() : QueryBuilder
    {
        $queryBuilder = $this->getDatabaseConnection()->getQueryBuilderForTable($this->tcaTableService->getTableName());
        $where = $this->tcaTableService->getWhereClause();
        $query = $queryBuilder->select(... $this->tcaTableService->getFields())
            ->from($this->tcaTableService->getTableClause())
            ->where($where->getStatement())
            ->setParameters($where->getParameters());

        foreach ($this->tcaTableService->getJoins() as $join) {
            $query->from($join->getTable());
            $query->andWhere($join->getCondition());
        }

        return $query;
    }

    protected function getDatabaseConnection()
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}
