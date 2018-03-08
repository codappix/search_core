<?php
namespace Codappix\SearchCore\Hook;

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

use Codappix\SearchCore\Configuration\NoConfigurationException;
use Codappix\SearchCore\Domain\Service\DataHandler as OwnDataHandler;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler as CoreDataHandler;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Wrapper for TYPO3 Hooks to internal API.
 */
class DataHandler implements Singleton
{
    /**
     * @var OwnDataHandler
     */
    protected $dataHandler;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * Dependency injection as TYPO3 doesn't provide it on it's own.
     * Still you can submit your own dataHandler.
     *
     * @param OwnDataHandler $dataHandler
     * @param Logger $logger
     */
    public function __construct(OwnDataHandler $dataHandler = null, Logger $logger = null)
    {
        $this->dataHandler = $dataHandler;
        if ($this->dataHandler === null) {
            try {
                $this->dataHandler = GeneralUtility::makeInstance(ObjectManager::class)
                    ->get(OwnDataHandler::class);
            } catch (NoConfigurationException $e) {
                // We have no configuration. That's fine, hooks will not be
                // executed due to check for existing DataHandler.
            }
        }

        $this->logger = $logger;
        if ($this->logger === null) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)
                ->getLogger(__CLASS__);
        }
    }

    /**
     * Called by CoreDataHandler on deletion of records.
     *
     * @param string $table
     * @param int $uid
     *
     * @return bool False if hook was not processed.
     */
    public function processCmdmap_deleteAction($table, $uid)
    {
        if (! $this->shouldProcessHookForTable($table)) {
            $this->logger->debug('Delete not processed.', [$table, $uid]);
            return false;
        }

        $this->dataHandler->delete($table, $uid);
        return true;
    }

    public function processDatamap_afterAllOperations(CoreDataHandler $dataHandler)
    {
        foreach ($dataHandler->datamap as $table => $record) {
            $uid = key($record);
            $fieldData = current($record);

            if (isset($fieldArray['uid'])) {
                $uid = $fieldArray['uid'];
            } elseif (isset($dataHandler->substNEWwithIDs[$uid])) {
                $uid = $dataHandler->substNEWwithIDs[$uid];
            }

            $this->processRecord($table, $uid);
        }
    }

    protected function processRecord(string $table, int $uid) : bool
    {
        if (! $this->shouldProcessHookForTable($table)) {
            $this->logger->debug('Indexing of record not processed.', [$table, $uid]);
            return false;
        }

        $record = $this->getRecord($table, $uid);
        if ($record !== null) {
            $this->dataHandler->update($table, $record);
            return true;
        }

        $this->logger->debug('Indexing of record not processed, as he was not found in Database.', [$table, $uid]);
        return false;
    }

    /**
     * @param string $table
     * @return bool
     */
    protected function shouldProcessHookForTable($table)
    {
        if ($this->dataHandler === null) {
            $this->logger->debug('Datahandler could not be setup.');
            return false;
        }
        if (! $this->dataHandler->canHandle($table)) {
            $this->logger->debug('Table is not allowed.', [$table]);
            return false;
        }

        return true;
    }

    /**
     * Wrapper to allow unit testing.
     *
     * @param string $table
     * @param int $uid
     * @return null|array<String>
     */
    protected function getRecord($table, $uid)
    {
        return BackendUtility::getRecord($table, $uid);
    }
}
