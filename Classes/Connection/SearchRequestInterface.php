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

use Codappix\SearchCore\Connection\ConnectionInterface;
use Codappix\SearchCore\Connection\FacetRequestInterface;
use Codappix\SearchCore\Domain\Search\SearchService;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

interface SearchRequestInterface extends QueryResultInterface
{
    /**
     * Returns the actual string the user searched for.
     */
    public function getSearchTerm() : string;

    public function hasFilter() : bool;

    public function getFilter() : array;

    public function setFilter(array $filter);

    /**
     * @return void
     */
    public function addFacet(FacetRequestInterface $facet);

    /**
     * @return array<FacetRequestInterface>
     */
    public function getFacets() : array;

    public function setLimit(int $limit);

    public function setOffset(int $offset);

    public function getLimit() : int;

    public function getOffset() : int;

    /**
     * Workaround for paginate widget support which will
     * use the request to build another search.
     *
     * @return void
     */
    public function setConnection(ConnectionInterface $connection);

    /**
     * Workaround for paginate widget support which will
     * use the request to build another search.
     *
     * @return void
     */
    public function setSearchService(SearchService $searchService);
}
