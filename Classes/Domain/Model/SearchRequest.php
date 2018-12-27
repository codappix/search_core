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
use Codappix\SearchCore\Connection\SearchResultInterface;
use Codappix\SearchCore\Domain\Search\SearchService;
use Codappix\SearchCore\Utility\ArrayUtility as CustomArrayUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

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
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * Used for QueryInterface implementation to allow execute method to work.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var SearchService
     */
    protected $searchService;

    /**
     * @param string $query
     */
    public function __construct(string $query = '')
    {
        $this->query = $query;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getSearchTerm(): string
    {
        return $this->query;
    }

    /**
     * Type hint necessary for extbase!
     *
     * @param array $filter
     */
    public function setFilter(array $filter)
    {
        $filter = ArrayUtility::removeArrayEntryByValue($filter, '');
        $this->filter = CustomArrayUtility::removeEmptyElementsRecursively($filter);
    }

    public function hasFilter(): bool
    {
        return count($this->filter) > 0;
    }

    public function getFilter(): array
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
    public function getFacets(): array
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

    // Extbase QueryInterface
    // Current implementation covers only paginate widget support.

    /**
     * @throws \InvalidArgumentException
     */
    public function execute($returnRawQueryResult = false)
    {
        if (!($this->connection instanceof ConnectionInterface)) {
            throw new \InvalidArgumentException(
                'Connection was not set before, therefore execute can not work. Use `setConnection` before.',
                1502197732
            );
        }
        if (!($this->searchService instanceof SearchService)) {
            throw new \InvalidArgumentException(
                'SearchService was not set before, therefore execute can not work. Use `setSearchService` before.',
                1520325175
            );
        }

        return $this->searchService->processResult($this->connection->search($this));
    }

    public function setLimit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    public function setOffset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getSource()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196146);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function setOrderings(array $orderings)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196163);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function matching($constraint)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196197);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function logicalAnd($constraint1)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196166);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function logicalOr($constraint1)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196198);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function logicalNot(\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $constraint)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196166);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function equals($propertyName, $operand, $caseSensitive = true)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196199);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function like($propertyName, $operand, $caseSensitive = true)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196167);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function contains($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196200);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function in($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196167);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function lessThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196201);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function lessThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196168);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function greaterThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196202);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function greaterThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196168);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getType()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196203);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function setQuerySettings(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196168);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getQuerySettings()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196205);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function count()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196169);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getOrderings()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196206);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getConstraint()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196171);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function isEmpty($propertyName)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196207);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function setSource(\TYPO3\CMS\Extbase\Persistence\Generic\Qom\SourceInterface $source)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196172);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getStatement()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196208);
    }
}
