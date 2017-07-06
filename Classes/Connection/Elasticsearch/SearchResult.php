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
use Codappix\SearchCore\Connection\SearchResultInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class SearchResult implements SearchResultInterface
{
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
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(\Elastica\ResultSet $result, ObjectManagerInterface $objectManager)
    {
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
     * @return array<FacetIterface>
     */
    public function getFacets()
    {
        $this->initFacets();

        return $this->facets;
    }

    /**
     * Returns the total sum of matching results.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->result->getTotalHits();
    }

    // Countable - Interface
    /**
     * Returns the total sum of results contained in this result.
     *
     * @return int
     */
    public function count()
    {
        return $this->result->count();
    }

    // Iterator - Interface
    public function current()
    {
        return $this->result->current();
    }

    public function next()
    {
        return $this->result->next();
    }

    public function key()
    {
        return $this->result->key();
    }

    public function valid()
    {
        return $this->result->valid();
    }

    public function rewind()
    {
        $this->result->rewind();
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
            $this->facets[] = $this->objectManager->get(Facet::class, $aggregationName, $aggregation);
        }
    }
}
