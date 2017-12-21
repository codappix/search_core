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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

interface SearchRequestInterface extends QueryInterface
{
    /**
     * Returns the actual string the user searched for.
     *
     * @return string
     */
    public function getSearchTerm();

    /**
     * @param array $filter
     */
    public function setFilter(array $filter);

    /**
     * @return bool
     */
    public function hasFilter();

    /**
     * @return array
     */
    public function getFilter();

    /**
     * @param FacetRequestInterface $facet
     */
    public function addFacet(FacetRequestInterface $facet);

    /**
     * @return array
     */
    public function getFacets();

    /**
     * @param SuggestRequestInterface $suggest
     */
    public function addSuggest(SuggestRequestInterface $suggest);

    /**
     * @return array
     */
    public function getSuggests();

    /**
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection);
}
