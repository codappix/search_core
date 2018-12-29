<?php

namespace Codappix\SearchCore\Domain\Search;

/*
 * Copyright (C) 2018 Benjamin Serfhos <benjamin@serfhos.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\SearchCore\Connection\SearchRequestInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;

/**
 * Service to process a search request.
 */
interface SearchServiceInterface
{
    /**
     * Fetches result for provided search request.
     */
    public function search(SearchRequestInterface $searchRequest): SearchResultInterface;

    /**
     * Processes the result, e.g. applies configured data processing to result.
     */
    public function processResult(SearchResultInterface $searchResult): SearchResultInterface;
}
