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

use Leonmrni\SearchCore\Connection\Elasticsearch\TypeMapper;
use TYPO3\CMS\Core\SingletonInterface as Singleton;

/**
 * Factory to get types.
 */
class TypeFactory implements Singleton
{
    /**
     * @var MapperFactory
     */
    protected $mapperFactory;

    public function __construct(MapperFactory $mapperFactory)
    {
        $this->mapperFactory = $mapperFactory;
    }

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

        $this->mapType($type, $this->mapperFactory->getMapper($documentType));

        return $type;
    }

    /**
     * Creates or update mapping for given type using the given mapper.
     *
     * @param \Elastica\Type $type
     * @param TypeMapper\MapperInterface @mapper
     */
    protected function mapType(\Elastica\Type $type, TypeMapper\MapperInterface $mapper)
    {
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($type);
        $mapping->setProperties($mapper->getPropertyMapping());
        $mapping->send();
    }
}
