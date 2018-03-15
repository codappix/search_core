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
     */
    public function __construct(OwnDataHandler $dataHandler = null, Logger $logger = null)
    {
        if ($dataHandler === null) {
            try {
                $dataHandler = GeneralUtility::makeInstance(ObjectManager::class)
                    ->get(OwnDataHandler::class);
            } catch (NoConfigurationException $e) {
                // We have no configuration. That's fine, hooks will not be
                // executed due to check for existing DataHandler.
            }
        }
        $this->dataHandler = $dataHandler;

        if ($logger === null) {
            $logger = GeneralUtility::makeInstance(LogManager::class)
                ->getLogger(__CLASS__);
        }
        $this->logger = $logger;
    }

    /**
     * Called by CoreDataHandler on deletion of records.
     */
    public function processCmdmap_deleteAction(string $table, int $uid) : bool
    {
        if (! $this->shouldProcessHookForTable($table)) {
            $this->logger->debug('Delete not processed.', [$table, $uid]);
            return false;
        }

        $this->dataHandler->delete($table, (string) $uid);
        return true;
    }

    public function processDatamap_afterAllOperations(CoreDataHandler $dataHandler)
    {
        foreach ($dataHandler->datamap as $table => $record) {
            $uid = key($record);
            $fieldData = current($record);

            if (isset($fieldData['uid'])) {
                $uid = $fieldData['uid'];
            } elseif (isset($dataHandler->substNEWwithIDs[$uid])) {
                $uid = $dataHandler->substNEWwithIDs[$uid];
            }

            $this->processRecord($table, $uid);
        }
    }

    public function clearCachePostProc(array $parameters, CoreDataHandler $dataHandler)
    {
        $pageUid = 0;

        // If editor uses "small page blizzard"
        if (isset($parameters['cacheCmd']) && is_numeric($parameters['cacheCmd'])) {
            $pageUid = $parameters['cacheCmd'];
        }
        // If records were changed
        if (isset($parameters['uid_page']) && is_numeric($parameters['uid_page'])) {
            $pageUid = $parameters['uid_page'];
        }

        if ($pageUid > 0) {
            $this->processRecord('pages', (int) $pageUid);
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

    protected function shouldProcessHookForTable(string $table) : bool
    {
        if ($this->dataHandler === null) {
            $this->logger->debug('Datahandler could not be setup.');
            return false;
        }
        if (! $this->dataHandler->supportsTable($table)) {
            $this->logger->debug('Table is not allowed.', [$table]);
            return false;
        }

        return true;
    }

    /**
     * Wrapper to allow unit testing.
     *
     * @return array|null
     */
    protected function getRecord(string $table, int $uid)
    {
        return BackendUtility::getRecord($table, $uid);
    }
}
