<?php
namespace Leonmrni\SearchCore\Domain\Model;

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

use Leonmrni\SearchCore\Connection\FacetRequestInterface;
use Leonmrni\SearchCore\Connection\SearchRequestInterface;

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
    protected $query = '';

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var array
     */
    protected $facets = [];

    /**
     * @param string $query
     */
    public function __construct($query)
    {
        $this->query = (string) $query;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->query;
    }

    /**
     * @param array $filter
     */
    public function setFilter(array $filter)
    {
        $this->filter = array_filter(array_map('strval', $filter));
    }

    /**
     * @return bool
     */
    public function hasFilter()
    {
        return count($this->filter) > 0;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Add a facet to gather in this search request.
     *
     * @param FacetRequestInterface $facet
     */
    public function addFacet(FacetRequestInterface $facet)
    {
        $this->facets[$facet->getIdentifier()] = $facet;
    }

    /**
     * Returns all configured facets to fetch in this search request.
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }
}
