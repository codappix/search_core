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

use Codappix\SearchCore\Connection\FacetRequestInterface;

class FacetRequest implements FacetRequestInterface
{
    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * As the facets come from configuration this might be a good idea to help
     * integrators find issues.
     */
    public function __construct(string $identifier, array $config)
    {
        $this->identifier = $identifier;
        $this->config = $config;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
