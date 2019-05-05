<?php
namespace Codappix\SearchCore\Domain\Model;

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
use Codappix\SearchCore\Connection\SearchRequestInterface;
use Codappix\SearchCore\Domain\Model\Query;
use Codappix\SearchCore\Domain\Search\SearchService;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Represents a search request used to process an actual search.
 */
class SearchRequest implements SearchRequestInterface
{
    /**
     * The search string provided by the user, the actual term to search for.
     *
     * @var string
     */
    protected $queryString = '';

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var array
     */
    protected $facets = [];

    /**
     * @var Query
     */
    private $query;

    /**
     * Used for QueryInterface implementation to allow execute method to work.
     *
     * @var ConnectionInterface
     */
    protected $connection = null;

    /**
     * @var SearchService
     */
    protected $searchService = null;

    /**
     * @param string $query
     */
    public function __construct(string $queryString = '')
    {
        $this->queryString = $queryString;
        $this->query = new Query();
    }

    public function getSearchTerm() : string
    {
        return $this->queryString;
    }

    /**
     * @param array $filter
     */
    public function setFilter(array $filter)
    {
        $filter = \TYPO3\CMS\Core\Utility\ArrayUtility::removeArrayEntryByValue($filter, '');
        $this->filter = \TYPO3\CMS\Core\Utility\ArrayUtility::filterRecursive($filter, function ($value) {
            return (!is_array($value) && trim($value) !== '')
                || is_array($value) && count($value) !== 0;
        });
    }

    public function hasFilter() : bool
    {
        return count($this->filter) > 0;
    }

    public function getFilter() : array
    {
        return $this->filter;
    }

    /**
     * Add a facet to gather in this search request.
     */
    public function addFacet(FacetRequestInterface $facet)
    {
        $this->facets[$facet->getIdentifier()] = $facet;
    }

    /**
     * Returns all configured facets to fetch in this search request.
     */
    public function getFacets() : array
    {
        return $this->facets;
    }

    /**
     * Define connection to use for this request.
     * Necessary to allow implementation of execute for interface.
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function setSearchService(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function setLimit(int $limit)
    {
        $this->query->setLimit($limit);

        return $this;
    }

    public function setOffset(int $offset)
    {
        $this->query->setOffset($offset);

        return $this;
    }

    public function getLimit() : int
    {
        return $this->query->getLimit();
    }

    public function getOffset() : int
    {
        return $this->query->getOffset();
    }

    // Implementation of QueryResultInterface

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    /**
     * Returns the first object in the result set
     *
     * @return object
     */
    public function getFirst()
    {
    }

    /**
     * Returns an array with the objects in the result set
     *
     * @return array
     */
    public function toArray()
    {
    }

    public function count(): int
    {
    }

    public function current(): mixed
    {
    }

    public function key()
    {
    }

    public function next(): void
    {
    }

    public function rewind(): void
    {
    }

    public function valid(): bool
    {
    }

    public function offsetExists($offset): bool
    {
        // TODO: Implement
        return false;
    }

    public function offsetGet($offset): mixed
    {
    }

    public function offsetSet($offset, $value) : void
    {
    }

    public function offsetUnset($offset): void
    {
    }
}
