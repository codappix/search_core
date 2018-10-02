<?php

namespace Codappix\SearchCore\Domain\Model;

/*
 * Copyright (C) 2018  Daniel Siepmann <coding@daniel-siepmann.de>
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
 * As we have to stay compatible with QueryResultInterface
 * of extbase but can and need not to provide all methods,
 * this stub will provde the non implemented methods to
 * keep real implementations clean.
 */
trait QueryResultInterfaceStub
{
    /**
     * @throws \BadMethodCallException
     */
    public function getFirst()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502195121);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function toArray()
    {
        throw new \BadMethodCallException('Method is not implemented yet.', 1502195135);
    }

    /**
     * @param $offset
     * @return boolean
     * @throws \BadMethodCallException
     */
    public function offsetExists($offset)
    {
        // Return false to allow Fluid to use appropriate getter methods.
        return false;
    }

    /**
     * @param $offset
     * @throws \BadMethodCallException
     */
    public function offsetGet($offset)
    {
        throw new \BadMethodCallException('Use getter to fetch properties.', 1502196933);
    }

    /**
     * @param $offset
     * @param $value
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('You are not allowed to modify the result.', 1502196934);
    }

    /**
     * @param $offset
     * @throws \BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('You are not allowed to modify the result.', 1502196936);
    }
}
