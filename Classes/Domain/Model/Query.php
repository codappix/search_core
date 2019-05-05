<?php

namespace Codappix\SearchCore\Domain\Model;

/*
 * Copyright (C) 2019 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Extbase QueryInterface
 * Current implementation covers only paginate widget support.
 */
class Query implements QueryInterface
{
    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var int
     */
    private $limit = 10;

    public function execute($returnRawQueryResult = false)
    {
        if (! ($this->connection instanceof ConnectionInterface)) {
            throw new \InvalidArgumentException(
                'Connection was not set before, therefore execute can not work. Use `setConnection` before.',
                1502197732
            );
        }
        if (! ($this->searchService instanceof SearchService)) {
            throw new \InvalidArgumentException(
                'SearchService was not set before, therefore execute can not work. Use `setSearchService` before.',
                1520325175
            );
        }

        return $this->searchService->processResult($this->connection->search($this));
    }

    public function setLimit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    public function setOffset($offset)
    {
        $this->offset = (int) $offset;

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
        throw new \BadMethodCallException('Method is not implemented yet.', 1502196199);
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
