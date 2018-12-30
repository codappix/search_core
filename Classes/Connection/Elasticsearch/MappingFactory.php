<?php

namespace Codappix\SearchCore\Connection\Elasticsearch;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
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
use Codappix\SearchCore\Configuration\InvalidArgumentException;
use TYPO3\CMS\Core\SingletonInterface as Singleton;

/**
 * Factory to get mappings.
 */
class MappingFactory implements Singleton
{
    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @var TypeFactory
     */
    protected $typeFactory;

    /**
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(
        ConfigurationContainerInterface $configuration,
        TypeFactory $typeFactory
    ) {
        $this->configuration = $configuration;
        $this->typeFactory = $typeFactory;
    }

    /**
     * Get an mapping based on type.
     */
    public function getMapping(string $documentType): \Elastica\Type\Mapping
    {
        $type = $this->typeFactory->getType($documentType);

        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($type);

        $configuration = $this->getConfiguration($documentType);
        $mapping->setProperties($configuration);

        return $mapping;
    }

    private function getConfiguration(string $identifier): array
    {
        try {
            return $this->configuration->get('indexing.' . $identifier . '.mapping');
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
}
