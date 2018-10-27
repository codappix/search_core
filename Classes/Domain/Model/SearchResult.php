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
    protected $results;

    /**
     * For Iterator interface.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * SearchResult constructor.
     * @param SearchResultInterface $originalSearchResult
     * @param array $resultItems
     */
    public function __construct(SearchResultInterface $originalSearchResult, array $resultItems)
    {
        $this->originalSearchResult = $originalSearchResult;
        $this->resultItems = $resultItems;
    }

    /**
     * @return array<ResultItemInterface>
     */
    public function getResults(): array
    {
        $this->initResults();

        return $this->results;
    }

    /**
     * @return void
     */
    protected function initResults()
    {
        if ($this->results === null) {
            foreach ($this->resultItems as $item) {
                $this->results[] = new ResultItem($item['data'], $item['type']);
            }
        }
    }

    /**
     * @return array
     */
    public function getFacets(): array
    {
        return $this->originalSearchResult->getFacets();
    }

    /**
     * @return integer
     */
    public function getCurrentCount(): int
    {
        return $this->originalSearchResult->getCurrentCount();
    }

    /**
     * @return integer
     */
    public function count()
    {
        return $this->originalSearchResult->count();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->getResults()[$this->position];
    }

    /**
     * @return mixed
     */
    public function next()
    {
        ++$this->position;

        return $this->current();
    }

    /**
     * @return integer|mixed
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->getResults()[$this->position]);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function getQuery()
    {
        return $this->originalSearchResult->getQuery();
    }
}
