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
 * A search result.
 */
interface SearchResultInterface extends \Iterator, \Countable
{
    /**
     * @return array<ResultItemInterface>
     */
    public function getResults();

    /**
     * Return all facets, if any.
     *
     * @return array<FacetIterface>
     */
    public function getFacets();

    /**
     * Returns the total sum of matching results.
     *
     * @return int
     */
    public function getTotalCount();

    // Countable - Interface

    /**
     * Returns the total sum of results contained in this result.
     *
     * @return int
     */
    public function count();
}
