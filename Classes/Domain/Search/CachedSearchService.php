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
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Service to process a search request, only once per request.
 */
class CachedSearchService implements SingletonInterface, SearchServiceInterface
{
    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var SearchService
     */
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(SearchRequestInterface $searchRequest): SearchResultInterface
    {
        $hash = $this->getHash($searchRequest);
        if (isset($this->results[$hash]) && $this->results[$hash] instanceof SearchResultInterface) {
            return $this->results[$hash];
        }
        return $this->results[$hash] = $this->searchService->search($searchRequest);
    }

    public function processResult(SearchResultInterface $searchResult): SearchResultInterface
    {
        return $this->searchService->processResult($searchResult);
    }

    protected function getHash(SearchRequestInterface $searchRequest): string
    {
        if (is_callable([$searchRequest, 'getRequestHash'])) {
            return (string)$searchRequest->getRequestHash();
        }
        return sha1(serialize($searchRequest));
    }
}
