<?php
namespace Leonmrni\SearchCore\Command;

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

use Leonmrni\SearchCore\Domain\Index\IndexerFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Command controller to provide indexing through CLI.
 */
class IndexCommandController extends CommandController
{
    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var \Leonmrni\SearchCore\Configuration\ConfigurationContainerInterface
     * @inject
     */
    protected $configuration;

    /**
     * @param IndexerFactory $factory
     */
    public function injectIndexerFactory(IndexerFactory $factory)
    {
        $this->indexerFactory = $factory;
    }

    /**
     * Will index the given table or everything.
     *
     * @param string $table
     */
    public function indexCommand($table)
    {
        // TODO: Allow to index multiple tables at once?
        // TODO: Also allow to index everything?
        if (! in_array($table, GeneralUtility::trimExplode(',', $this->configuration->get('index', 'allowedTables')))) {
            $this->outputLine('Table is not allowed for indexing.');
            $this->quit(1);
        }
        $this->indexerFactory->getIndexer($table)->indexAllDocuments();
        $this->outputLine('Table was indexed.');
    }
}
