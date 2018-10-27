<?php

namespace Codappix\SearchCore\Domain\Model;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
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

class ResultItem implements ResultItemInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $type = '';

    /**
     * ResultItem constructor.
     * @param array $result
     * @param string $type
     */
    public function __construct(array $result, string $type)
    {
        $this->data = $result;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getPlainData(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('It\'s not possible to change the search result.', 1499179077);
    }

    /**
     * @param mixed $offset
     * @throws \BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('It\'s not possible to change the search result.', 1499179077);
    }
}
