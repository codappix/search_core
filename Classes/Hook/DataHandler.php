<?php
namespace Leonmrni\SearchCore\Hook;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler as CoreDataHandler;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Leonmrni\SearchCore\Service\DataHandler as OwnDataHandler;

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
     * TODO: Inject datahandler only on use?! Using getter / setter or something else?
     * Otherwise a connection to elastic and whole bootstrapping will be triggered.
     *
     * @param OwnDataHandler $dataHandler
     * @param Logger $logger
     */
    public function __construct(OwnDataHandler $dataHandler = null, Logger $logger = null)
    {
        $this->dataHandler = $dataHandler;
        if ($this->dataHandler === null) {
            $this->dataHandler = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(OwnDataHandler::class);
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
     * @param array $record
     * @param bool $recordWasDeleted
     * @param CoreDataHandler $dataHandler
     */
    public function processCmdmap_deleteAction($table, $uid, array $record, $recordWasDeleted, CoreDataHandler $dataHandler)
    {
        if (! $this->shouldProcessTable($table)) {
            $this->logger->debug('Delete not processed, cause table is not allowed.', [$table]);
            return;
        }

        $this->dataHandler->delete($table, $uid);
    }

    /**
     * Called by CoreDataHandler on database operations, e.g. if new records were created or records were updated.
     *
     * @param string $status
     * @param string $table
     * @param string|int $uid
     * @param array $fieldArray
     * @param CoreDataHandler $dataHandler
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $uid, array $fieldArray, CoreDataHandler $dataHandler)
    {
        if (! $this->shouldProcessTable($table)) {
            $this->logger->debug('Database update not processed, cause table is not allowed.', [$table]);
            return;
        }

        if ($status === 'new') {
            $this->dataHandler->add($table, $dataHandler->substNEWwithIDs[$uid], $fieldArray);
            return;
        }

        if ($status === 'update') {
            $this->dataHandler->update(
                $table,
                $uid,
                $this->getRecord($table, $uid)
            );
            return;
        }

        $this->logger->debug('Database update not processed, cause status is unhandled.', [$status, $table, $uid, $fieldArray]);
    }

    /**
     * Returns array containing tables that should be processed by this hook.
     *
     * TODO: Fetch from config
     *
     * @return array
     */
    protected function getTablesToProcess()
    {
        return [
            'tt_content',
        ];
    }

    /**
     * @param string $table
     * @return bool
     */
    protected function shouldProcessTable($table)
    {
        return in_array($table, $this->getTablesToProcess());
    }

    /**
     * Wrapper to allow unit testing.
     *
     * @param string $table
     * @param int $uid
     * @return array
     */
    protected function getRecord($table, $uid)
    {
        return BackendUtility::getRecord($table, $uid);
    }
}
