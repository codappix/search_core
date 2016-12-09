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

/**
 * Defines interface for connections to storage backend for interacting with documents.
 */
interface ConnectionInterface
{
    /**
     * Will add a new document, based on his identifier and record type.
     *
     * @param string $recordType
     * @param int $identifier
     * @param array $record
     *
     * @return
     */
    public function add($recordType, $identifier, array $record);

    /**
     * Add the given records.
     *
     * @param string $recordType
     * @param array $records
     */
    public function addDocuments($recordType, array $records);

    /**
     * Will update an existing document, based on his identifier and record type.
     *
     * @param string $recordType
     * @param int $identifier
     * @param array $record
     *
     * @return
     */
    public function update($recordType, $identifier, array $record);

    /**
     * Will remove an existing document, based on his identifier and record type.
     *
     * @param string $recordType
     * @param int $identifier
     *
     * @return
     */
    public function delete($recordType, $identifier);

    /**
     * Search by given request and return result.
     *
     * @param SearchRequestInterface $searchRequest
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $searchRequest);
}
