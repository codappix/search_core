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
    protected $options;

    /**
     * Facet constructor.
     * @param string $name
     * @param array $aggregation
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(string $name, array $aggregation, ConfigurationContainerInterface $configuration)
    {
        $this->name = $name;
        $this->buckets = $aggregation['buckets'];

        $config = $configuration->getIfExists('searching.facets.' . $this->name) ?: [];
        foreach ($config as $configEntry) {
            if (isset($configEntry['field'])) {
                $this->field = $configEntry['field'];
                break;
            }
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Returns all possible options for this facet.
     *
     * @return array<FacetOptionInterface>
     */
    public function getOptions(): array
    {
        $this->initOptions();

        return $this->options;
    }

    /**
     * @return void
     */
    protected function initOptions()
    {
        if (is_array($this->options)) {
            return;
        }

        $this->options = [];
        foreach ($this->buckets as $bucket) {
            $this->options[$bucket['key']] = new FacetOption($bucket);
        }
    }
}
