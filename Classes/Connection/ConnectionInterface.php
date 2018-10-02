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

/**
 * Defines interface for connections to storage backend for interacting with documents.
 */
interface ConnectionInterface
{
    /**
     * Will add a new document.
     *
     * @param string $documentType
     * @param array $document
     * @return void
     */
    public function addDocument(string $documentType, array $document);

    /**
     * Add the given documents.
     *
     * @param string $documentType
     * @param array $documents
     * @return void
     */
    public function addDocuments(string $documentType, array $documents);

    /**
     * Will update an existing document.
     *
     * NOTE: Batch updating is not yet supported.
     *
     * @param string $documentType
     * @param array $document
     * @return void
     */
    public function updateDocument(string $documentType, array $document);

    /**
     * Will remove an existing document.
     *
     * NOTE: Batch deleting is not yet supported.
     *
     * @param string $documentType
     * @param string $identifier
     * @return void
     */
    public function deleteDocument(string $documentType, string $identifier);

    /**
     * Search by given request and return result.
     *
     * @param SearchRequestInterface $searchRequest
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $searchRequest): SearchResultInterface;

    /**
     * Will delete the whole index / db.
     *
     * @param string $documentType
     * @return void
     */
    public function deleteIndex(string $documentType);
}
