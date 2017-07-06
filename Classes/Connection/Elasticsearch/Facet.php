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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Connection\FacetInterface;

class Facet implements FacetInterface
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $field = '';

    /**
     * @var array
     */
    protected $buckets = [];

    /**
     * @var array<FacetOption>
     */
    protected $options = [];

    public function __construct($name, array $aggregation, ConfigurationContainerInterface $configuration)
    {
        $this->name = $name;
        $this->buckets = $aggregation['buckets'];
        $this->field = $configuration->getIfExists('searching.facets.' . $this->name . '.field') ?: '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns all possible options for this facet.
     *
     * @return array<FacetOptionInterface>
     */
    public function getOptions()
    {
        $this->initOptions();

        return $this->options;
    }

    protected function initOptions()
    {
        if ($this->options !== []) {
            return;
        }

        foreach ($this->buckets as $bucket) {
            $this->options[] = new FacetOption($bucket);
        }
    }
}
