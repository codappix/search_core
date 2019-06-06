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

use Codappix\SearchCore\Connection\ResultItemInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use Codappix\SearchCore\Domain\Model\QueryResultInterfaceStub;

/**
 * Generic model for mapping a concrete search result from a connection.
 */
class SearchResult implements SearchResultInterface
{
    use QueryResultInterfaceStub;

    /**
     * @var SearchResultInterface
     */
    protected $originalSearchResult;

    /**
     * @var array
     */
    protected $resultItems = [];

    /**
     * @var array
     */
    protected $results = [];

    /**
     * For Iterator interface.
     *
     * @var int
     */
    protected $position = 0;

    public function __construct(SearchResultInterface $originalSearchResult, array $resultItems)
    {
        $this->originalSearchResult = $originalSearchResult;
        $this->resultItems = $resultItems;
    }

    /**
     * @return array<ResultItemInterface>
     */
    public function getResults() : array
    {
        $this->initResults();

        return $this->results;
    }

    protected function initResults()
    {
        if ($this->results !== []) {
            return;
        }

        foreach ($this->resultItems as $item) {
            $this->results[] = new ResultItem($item['data'], $item['type']);
        }
    }

    public function getFacets() : array
    {
        return $this->originalSearchResult->getFacets();
    }

    public function getCurrentCount() : int
    {
        return $this->originalSearchResult->getCurrentCount();
    }

    public function count()
    {
        return $this->originalSearchResult->count();
    }

    public function current()
    {
        return $this->getResults()[$this->position];
    }

    public function next()
    {
        ++$this->position;
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

    public function getQuery()
    {
        return $this->originalSearchResult->getQuery();
    }
}
