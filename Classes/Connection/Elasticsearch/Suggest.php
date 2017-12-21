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
use Codappix\SearchCore\Connection\SuggestInterface;

class Suggest implements SuggestInterface
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var array<SuggestOption>
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $rawOptions = [];

    public function __construct($name, array $suggest, ConfigurationContainerInterface $configuration)
    {
        $this->name = $name;
        $this->rawOptions = $suggest[0]['options'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        $this->initOptions();

        return $this->options;
    }

    public function getUniqueOptions()
    {
        $this->initOptions();

        return array_unique($this->options);
    }

    protected function initOptions()
    {
        if ($this->options !== []) {
            return;
        }

        foreach ($this->rawOptions as $rawOption) {
            $this->options[] = new SuggestOption($rawOption);
        }
    }
}
