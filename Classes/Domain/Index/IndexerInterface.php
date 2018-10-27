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

/**
 * Interface that all indexer should implement.
 */
interface IndexerInterface
{
    /**
     * Fetches all documents from the indexerService and pushes it to the connection.
     */
    public function indexAllDocuments();

    /**
     * Fetches a single document and pushes it to the connection.
     */
    public function indexDocument(string $identifier);

    /**
     * Delete the whole index.
     */
    public function delete();

    /**
     * Delete the whole index.
     */
    public function deleteDocuments();

    /**
     * Receives the identifier of the indexer itself.
     */
    public function setIdentifier(string $identifier);

    /**
     * Returnes the identifier of the indexer.
     */
    public function getIdentifier(): string;
}
