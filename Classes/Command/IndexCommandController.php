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
use Codappix\SearchCore\Domain\Index\IndexerInterface;
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
     * @return void
     */
    public function injectIndexerFactory(IndexerFactory $factory)
    {
        $this->indexerFactory = $factory;
    }

    /**
     * Will index all documents for the given identifiers.
     *
     * @param string $identifier Comma separated list of identifiers.
     * @return void
     */
    public function indexCommand(string $identifiers)
    {
        $this->executeForIdentifier($identifiers, function (IndexerInterface $indexer) {
            $indexer->indexAllDocuments();
            $this->outputLine('Documents in indice ' . $indexer->getIdentifier() . ' were indexed.');
        });
    }

    /**
     * Will delete all indexed documents for the given identifiers.
     *
     * @param string $identifier Comma separated list of identifiers.
     * @return void
     */
    public function deleteCommand(string $identifiers)
    {
        $this->executeForIdentifier($identifiers, function (IndexerInterface $indexer) {
            $indexer->deleteDocuments();
            $this->outputLine('Documents in indice ' . $indexer->getIdentifier() . ' were deleted.');
        });
    }

    /**
     * Will delete the full index for given identifiers.
     *
     * @param string $identifier Comma separated list of identifiers.
     * @return void
     */
    public function flushCommand(string $identifiers = 'pages')
    {
        $this->executeForIdentifier($identifiers, function (IndexerInterface $indexer) {
            $indexer->delete();
            $this->outputLine('Indice ' . $indexer->getIdentifier() . ' was deleted.');
        });
    }

    /**
     * Executes the given callback method for each provided identifier.
     *
     * An indexer is created for each identifier, which is provided as first argument to the callback.
     */
    private function executeForIdentifier(string $identifiers, callable $callback)
    {
        foreach (GeneralUtility::trimExplode(',', $identifiers, true) as $identifier) {
            try {
                $callback($this->indexerFactory->getIndexer($identifier));
            } catch (NoMatchingIndexerException $e) {
                $this->outputLine('No indexer found for: ' . $identifier . '.');
            }
        }
    }
}
