<?php
namespace Codappix\SearchCore\Command;

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

use Codappix\SearchCore\Domain\Index\IndexerFactory;
use Codappix\SearchCore\Domain\Index\NoMatchingIndexerException;
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
     * @param IndexerFactory $factory
     */
    public function injectIndexerFactory(IndexerFactory $factory)
    {
        $this->indexerFactory = $factory;
    }

    /**
     * Will index the given identifier.
     *
     * @param string $identifier
     */
    public function indexCommand($identifier)
    {
        try {
            $this->indexerFactory->getIndexer($identifier)->indexAllDocuments();
            $this->outputLine($identifier . ' was indexed.');
        } catch (NoMatchingIndexerException $e) {
            $this->outputLine('No indexer found for: ' . $identifier);
        }
    }

    /**
     * Will delete the given identifier.
     *
     * @param string $identifier
     */
    public function deleteCommand($identifier)
    {
        try {
            $this->indexerFactory->getIndexer($identifier)->delete();
            $this->outputLine($identifier . ' was deleted.');
        } catch (NoMatchingIndexerException $e) {
            $this->outputLine('No indexer found for: ' . $identifier);
        }
    }
}
