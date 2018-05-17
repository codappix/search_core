<?php
namespace Codappix\SearchCore\Domain\Index\TcaIndexer;

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

interface TcaTableServiceInterface
{
    public function getTableName() : string;

    public function getTableClause() : string;

    /**
     * Filter the given records by root line blacklist settings.
     */
    public function filterRecordsByRootLineBlacklist(array &$records);

    public function prepareRecord(array &$record);

    /**
     * @throws InvalidArgumentException
     */
    public function getColumnConfig(string $columnName) : array;

    public function getRecords(int $offset, int $limit) : array;

    public function getRecord(int $identifier) : array;

    public function getLanguageUidColumn() : string;
}
