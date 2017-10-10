<?php
namespace Codappix\SearchCore\Connection\Elasticsearch;

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

use Codappix\SearchCore\Connection\FacetInterface;
use Codappix\SearchCore\Connection\ResultItemInterface;
use Codappix\SearchCore\Connection\SearchRequestInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class SearchResult implements SearchResultInterface
{
    /**
     * @var SearchRequestInterface
     */
    protected $searchRequest;

    /**
     * @var \Elastica\ResultSet
     */
    protected $result;

    /**
     * @var array<FacetInterface>
     */
    protected $facets = [];

    /**
     * @var array<ResultItemInterface>
     */
    protected $results = [];

    /**
     * For Iterator interface.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(
        SearchRequestInterface $searchRequest,
        \Elastica\ResultSet $result,
        ObjectManagerInterface $objectManager
    ) {
        $this->searchRequest = $searchRequest;
        $this->result = $result;
        $this->objectManager = $objectManager;
    }

    /**
     * @return array<ResultItemInterface>
     */
    public function getResults()
    {
        $this->initResults();

        return $this->results;
    }

    /**
     * Return all facets, if any.
     *
     * @return array<FacetInterface>
     */
    public function getFacets()
    {
        $this->initFacets();

        return $this->facets;
    }

    public function getCurrentCount()
    {
        return $this->result->count();
    }

    protected function initResults()
    {
        if ($this->results !== []) {
            return;
        }

        foreach ($this->result->getResults() as $result) {
            $this->results[] = new ResultItem($result);
        }
    }

    protected function initFacets()
    {
        if ($this->facets !== [] || !$this->result->hasAggregations()) {
            return;
        }

        foreach ($this->result->getAggregations() as $aggregationName => $aggregation) {
            $this->facets[$aggregationName] = $this->objectManager->get(Facet::class, $aggregationName, $aggregation);
        }
    }

    // Countable - Interface
    public function count()
    {
        return $this->result->getTotalHits();
    }

    // Iterator - Interface
    public function current()
    {
        return $this->getResults()[$this->position];
    }

    public function next()
    {
        ++$this->position;

        return $this->current();
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->getResults()[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    // Extbase QueryResultInterface - Implemented to support Pagination of Fluid.

    public function getQuery()
    {
        return $this->searchRequest;
    }

    public function getFirst()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502195121);
    }

    public function toArray()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502195135);
    }

    public function offsetExists($offset)
    {
        // Return false to allow Fluid to use appropriate getter methods.
        return false;
    }

    public function offsetGet($offset)
    {
        throw new \BadMethodCallException('Use getter to fetch properties.', 1502196933);
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('You are not allowed to modify the result.', 1502196934);
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('You are not allowed to modify the result.', 1502196936);
    }
}
