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
     */
    public function addDocument(string $documentType, array $document);

    /**
     * Add the given documents.
     */
    public function addDocuments(string $documentType, array $documents);

    /**
     * Will update an existing document.
     *
     * NOTE: Batch updating is not yet supported.
     */
    public function updateDocument(string $documentType, array $document);

    /**
     * Will remove an existing document.
     *
     * NOTE: Batch deleting is not yet supported.
     */
    public function deleteDocument(string $documentType, string $identifier);

    /**
     * Will all documents of certain kind / in certain index.
     */
    public function deleteAllDocuments(string $documentType);

    /**
     * Will delete the whole index / db.
     */
    public function deleteIndex(string $documentType);

    /**
     * Search by given request and return result.
     */
    public function search(SearchRequestInterface $searchRequest): SearchResultInterface;
}
