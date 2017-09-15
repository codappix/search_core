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
    protected $connection = null;

    /**
     * @param string $query
     */
    public function __construct($query = '')
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

    /**
     * Define connection to use for this request.
     * Necessary to allow implementation of execute for interface.
     *
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    // Extbase QueryInterface
    // Current implementation covers only paginate widget support.
    public function execute($returnRawQueryResult = false)
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->search($this);
        }

        throw new \InvalidArgumentException(
            'Connection was not set before, therefore execute can not work. Use `setConnection` before.',
            1502197732
        );
    }

    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
    }

    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getSource()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196146);
    }

    public function setOrderings(array $orderings)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196163);
    }

    public function matching($constraint)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196197);
    }

    public function logicalAnd($constraint1)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196166);
    }

    public function logicalOr($constraint1)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196198);
    }

    public function logicalNot(\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $constraint)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196166);
    }

    public function equals($propertyName, $operand, $caseSensitive = true)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196199);
    }

    public function like($propertyName, $operand, $caseSensitive = true)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196167);
    }

    public function contains($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196200);
    }

    public function in($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196167);
    }

    public function lessThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196201);
    }

    public function lessThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196168);
    }

    public function greaterThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196202);
    }

    public function greaterThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196168);
    }

    public function getType()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196203);
    }

    public function setQuerySettings(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196168);
    }

    public function getQuerySettings()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196205);
    }

    public function count()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196169);
    }

    public function getOrderings()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196206);
    }

    public function getConstraint()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196171);
    }

    public function isEmpty($propertyName)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196207);
    }

    public function setSource(\TYPO3\CMS\Extbase\Persistence\Generic\Qom\SourceInterface $source)
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196172);
    }

    public function getStatement()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196208);
    }
}
