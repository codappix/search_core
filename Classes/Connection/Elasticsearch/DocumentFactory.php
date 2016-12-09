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

/**
 * Factory to create documents to index in Elasticsearch.
 */
class DocumentFactory implements Singleton
{
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
     * Creates document from record.
     *
     * @param string $tableName
     * @param array $record
     *
     * @return \Elastica\Document
     */
    public function getDocument($tableName, array $record)
    {
        if (!isset($record['search_identifier'])) {
             throw new \Exception('No search_identifier provided for record.', 1481194385);
        }

        $identifier = $record['search_identifier'];
        unset($record['search_identifier']);

        $this->logger->debug('Convert record to document', [$identifier, $record]);
        return new \Elastica\Document($identifier, $record);
    }

    /**
     * Creates documents based on records.
     *
     * @param string $tableName
     * @param array $records
     *
     * @return array
     */
    public function getDocuments($tableName, array $records)
    {
        foreach ($records as &$record) {
            $record = $this->getDocument($tableName, $record);
        }

        return $records;
    }
}
