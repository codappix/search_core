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

use Codappix\SearchCore\Domain\Search\SearchService;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

interface SearchRequestInterface extends QueryInterface
{
    /**
     * Returns the actual string the user searched for.
     *
     * @return string
     */
    public function getSearchTerm(): string;

    /**
     * @return bool
     */
    public function hasFilter(): bool;

    /**
     * @return array
     */
    public function getFilter(): array;

    /**
     * @param array $filter
     * @return void
     */
    public function setFilter(array $filter);

    /**
     * @param FacetRequestInterface $facet
     * @return void
     */
    public function addFacet(FacetRequestInterface $facet);

    /**
     * @return array<FacetRequestInterface>
     */
    public function getFacets(): array;

    /**
     * Workaround for paginate widget support which will
     * use the request to build another search.
     *
     * @param ConnectionInterface $connection
     * @return void
     */
    public function setConnection(ConnectionInterface $connection);

    /**
     * Workaround for paginate widget support which will
     * use the request to build another search.
     *
     * @param SearchService $searchService
     * @return void
     */
    public function setSearchService(SearchService $searchService);
}
