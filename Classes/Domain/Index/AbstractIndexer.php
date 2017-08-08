<?php
namespace Codappix\SearchCore\Domain\Index;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Configuration\InvalidArgumentException;
use Codappix\SearchCore\Connection\ConnectionInterface;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractIndexer implements IndexerInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $identifier = '';

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

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param ConnectionInterface $connection
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(ConnectionInterface $connection, ConfigurationContainerInterface $configuration)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
    }

    public function indexAllDocuments()
    {
        $this->logger->info('Start indexing');
        foreach ($this->getRecordGenerator() as $records) {
            if ($records === null) {
                break;
            }

            foreach ($records as &$record) {
                $this->prepareRecord($record);
            }

            $this->logger->debug('Index records.', [$records]);
            $this->connection->addDocuments($this->getDocumentName(), $records);
        }
        $this->logger->info('Finish indexing');
    }

    public function indexDocument($identifier)
    {
        $this->logger->info('Start indexing single record.', [$identifier]);
        try {
            $record = $this->getRecord($identifier);
            $this->prepareRecord($record);

            $this->connection->addDocument($this->getDocumentName(), $record);
        } catch (NoRecordFoundException $e) {
            $this->logger->info('Could not index document.', [$e->getMessage()]);
        }
        $this->logger->info('Finish indexing');
    }

    /**
     * @return \Generator
     */
    protected function getRecordGenerator()
    {
        $offset = 0;
        $limit = $this->getLimit();

        while (($records = $this->getRecords($offset, $limit)) !== []) {
            yield $records;
            $offset += $limit;
        }
    }

    /**
     * @param array &$record
     */
    protected function prepareRecord(array &$record)
    {
        $record['search_abstract'] = '';

        try {
            $fieldsToUse = GeneralUtility::trimExplode(
                ',',
                $this->configuration->get('indexing.' . $this->identifier . '.abstractFields')
            );
            if (!$fieldsToUse) {
                return;
            }
            foreach ($fieldsToUse as $fieldToUse) {
                if (isset($record[$fieldToUse]) && trim($record[$fieldToUse])) {
                    $record['search_abstract'] = trim($record[$fieldToUse]);
                    break;
                }
            }
        } catch (InvalidArgumentException $e) {
            return;
        }
    }

    /**
     * Returns the limit to use to fetch records.
     *
     * @return int
     */
    protected function getLimit()
    {
        // TODO: Make configurable.
        return 50;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array|null
     */
    abstract protected function getRecords($offset, $limit);

    /**
     * @param int $identifier
     * @return array
     * @throws NoRecordFoundException If record could not be found.
     */
    abstract protected function getRecord($identifier);

    /**
     * @return string
     */
    abstract protected function getDocumentName();
}
