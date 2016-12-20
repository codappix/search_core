<?php
namespace Leonmrni\SearchCore\Connection\Elasticsearch;

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

use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Factory to get indexes.
 *
 * The factory will take care of configuration and creation of index if necessary.
 */
class TypeFactory implements Singleton
{
    /**
     * @var \Leonmrni\SearchCore\Configuration\ConfigurationContainerInterface
     * @inject
     */
    protected $configuration;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * Get an index bases on TYPO3 table name.
     *
     * @param \Elastica\Index $index
     * @param string $documentType
     *
     * @return \Elastica\Type
     */
    public function getType(\Elastica\Index $index, $documentType)
    {
        $type = $index->getType($documentType);

        $mapper = $this->getMapper($type);
        if ($mapper !== null) {
            $this->mapType($type, $mapper);
        }

        return $type;
    }

    protected function getMapper(\Elastica\Type $type)
    {
        $config = $this->configuration->getIfExists('connections.elasticsearch.types.' . $type->getName() . '.mapping');
        if ($config === null) {
            return null;
        }

        if (class_exists($config)) {
            return $this->objectManager->get($config, $type->getName());
        }

        return null;
    }

    protected function mapType(\Elastica\Type $type, $mapper)
    {
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($type);
        $mapping->setProperties($mapper->getMapping());
        $mapping->send();
    }
}
