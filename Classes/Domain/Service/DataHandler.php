<?php
namespace Leonmrni\SearchCore\Domain\Service;

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

/**
 * Handles all data related things like updates, deletes and inserts.
 *
 * This is the place to add mappings of further parts to adjust the data before
 * sending ot to connection.
 */
class DataHandler implements Singleton
{
    /**
     * @var \Leonmrni\SearchCore\Connection\ConnectionInterface
     * @inject
     */
    protected $connection;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * Inject log manager to get concrete logger from it.
     *
     * @param \TYPO3\CMS\Core\Log\LogManager $logManager
     */
    public function injectLogger(\TYPO3\CMS\Core\Log\LogManager $logManager)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
    }

    /**
     * @param string $table
     * @param int $identifier
     */
    public function delete($table, $identifier)
    {
        $this->logger->debug('Record received for delete.', [$table, $identifier]);
        $this->connection->delete($table, $identifier);
    }

    /**
     * @param string $table
     * @param array $record
     */
    public function add($table, array $record)
    {
        $this->logger->debug('Record received for add.', [$table, $record]);
        $this->connection->add($table, $record);
    }

    /**
     * @param string $table
     */
    public function update($table, array $record)
    {
        $this->logger->debug('Record received for update.', [$table, $record]);
        $this->connection->update($table, $record);
    }
}
