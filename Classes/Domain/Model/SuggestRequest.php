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

use Codappix\SearchCore\Connection\SuggestRequestInterface;

class SuggestRequest implements SuggestRequestInterface
{
    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var string
     */
    protected $field = '';

    /**
     * TODO: Add validation / exception?
     * As the suggests come from configuration this might be a good idea to
     * help integrators find issues.
     *
     * @param string $identifier
     * @param string $field
     */
    public function __construct($identifier, $field)
    {
        $this->identifier = $identifier;
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
}
