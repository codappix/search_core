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

    public function __construct(array $result)
    {
        $this->data = $result;
    }

    public function getPlainData() : array
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('It\'s not possible to change the search result.', 1499179077);
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('It\'s not possible to change the search result.', 1499179077);
    }
}
