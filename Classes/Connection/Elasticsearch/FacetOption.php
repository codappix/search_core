<?php
namespace Codappix\SearchCore\Connection\Elasticsearch;

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

use Codappix\SearchCore\Connection\FacetOptionInterface;

class FacetOption implements FacetOptionInterface
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $displayName = '';

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @param array $bucket
     */
    public function __construct(array $bucket)
    {
        $this->name = $bucket['key'];
        $this->displayName = isset($bucket['key_as_string']) ? $bucket['key_as_string'] : $this->getName();
        $this->count = $bucket['doc_count'];
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getDisplayName() : string
    {
        return $this->displayName;
    }

    public function getCount() : int
    {
        return $this->count;
    }
}
