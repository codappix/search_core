<?php
namespace Leonmrni\SearchCore\Domain\Index;

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

use Leonmrni\SearchCore\Configuration\ConfigurationContainerInterface;
use Leonmrni\SearchCore\Configuration\InvalidArgumentException;
use Leonmrni\SearchCore\Domain\Index\IndexerInterface;
use Leonmrni\SearchCore\Domain\Index\TcaIndexer;
use Leonmrni\SearchCore\Domain\Index\TcaIndexer\TcaTableService;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Factory to get configured indexer based on configuration.
 */
class IndexerFactory implements Singleton
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigurationContainerInterface $configuration
    ) {
        $this->objectManager = $objectManager;
        $this->configuration = $configuration;
    }

    /**
     * @param string $identifier
     *
     * @return IndexerInterface
     * @throws NoMatchingIndexer
     */
    public function getIndexer($identifier)
    {
        try {
            return $this->buildIndexer($this->configuration->get('indexing.' . $identifier . '.indexer'), $identifier);
        } catch (NoMatchingIndexerException $e) {
            // Nothing to do, we throw exception below
        } catch (InvalidArgumentException $e) {
            // Nothing to do, we throw exception below
        }

        throw new NoMatchingIndexerException('Could not find an indexer for ' . $identifier, 1497341442);
    }

    /**
     * @param string $indexerClass
     * @param string $identifier
     *
     * @return IndexerInterface
     * @throws NoMatchingIndexer
     */
    protected function buildIndexer($indexerClass, $identifier)
    {
        if ($indexerClass === TcaIndexer::class) {
            return $this->objectManager->get(
                TcaIndexer::class,
                $this->objectManager->get(TcaTableService::class, $identifier)
            );
        }

        if (class_exists($indexerClass) && in_array(IndexerInterface::class, class_implements($indexerClass))) {
            return $this->objectManager->get($indexerClass);
        }

        throw new NoMatchingIndexerException('Could not find indexer: ' . $indexerClass, 1497341442);
    }
}
