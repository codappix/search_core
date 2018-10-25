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
use Elastica\Query;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @var \Codappix\SearchCore\DataProcessing\Service
     * @inject
     */
    protected $dataProcessorService;

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
     * @param string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * AbstractIndexer constructor.
     * @param ConnectionInterface $connection
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(ConnectionInterface $connection, ConfigurationContainerInterface $configuration)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
    }

    /**
     * @return void
     */
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

    /**
     * @param string $identifier
     * @return void
     */
    public function indexDocument(string $identifier)
    {
        $this->logger->info('Start indexing single record.', [$identifier]);
        try {
            $record = $this->getRecord((int)$identifier);
            $this->prepareRecord($record);

            $this->connection->addDocument($this->getDocumentName(), $record);
        } catch (NoRecordFoundException $e) {
            $this->logger->info('Could not index document. Try to delete it therefore.', [$e->getMessage()]);
            $this->connection->deleteDocument($this->getDocumentName(), $this->getIdentifier($identifier));
        }
        $this->logger->info('Finish indexing');
    }

    /**
     * @return void
     */
    public function delete()
    {
        $this->logger->info('Start deletion of index.');
        $this->connection->deleteIndex();
        $this->logger->info('Finish deletion.');
    }

    /**
     * @return void
     */
    public function deleteDocuments()
    {
        $this->logger->info('Start deletion of indexed documents.');
        $this->connection->deleteIndexByQuery(Query::create([
            'query' => [
                'term' => [
                    'search_document_type' => $this->getDocumentName()
                ]
            ]
        ]));
        $this->logger->info('Finish deletion.');
    }

    /**
     * @return \Generator
     */
    protected function getRecordGenerator()
    {
        $offset = 0;
        $limit = $this->getLimit();

        while (($records = $this->getRecords($offset, $limit)) !== null) {
            if (!empty($records)) {
                yield $records;
            }
            $offset += $limit;
        }
    }

    /**
     * @param array $record
     * @return void
     */
    protected function prepareRecord(array &$record)
    {
        try {
            foreach ($this->configuration->get('indexing.' . $this->identifier . '.dataProcessing') as $configuration) {
                $record = $this->dataProcessorService->executeDataProcessor($configuration, $record, $this->identifier);
            }
        } catch (InvalidArgumentException $e) {
            // Nothing to do.
        }
        $this->generateSearchIdentifiers($record);
        $this->handleAbstract($record);
    }

    /**
     * @param array $record
     * @return void
     */
    protected function generateSearchIdentifiers(array &$record)
    {
        if (!isset($record['search_document'])) {
            $record['search_document_type'] = $this->getDocumentName();
        }
        if (!isset($record['search_identifier']) && isset($record['uid'])) {
            $record['search_identifier'] = $this->getIdentifier($record['uid']);
        }
    }

    /**
     * @param array $record
     * @return void
     */
    protected function handleAbstract(array &$record)
    {
        $record['search_abstract'] = '';

        try {
            $fieldsToUse = GeneralUtility::trimExplode(
                ',',
                $this->configuration->get('indexing.' . $this->identifier . '.abstractFields')
            );
            if ($fieldsToUse === []) {
                throw new InvalidArgumentException('No fields to use', 1538487209251);
            }

            foreach ($fieldsToUse as $fieldToUse) {
                if (isset($record[$fieldToUse]) && trim($record[$fieldToUse])) {
                    $record['search_abstract'] = trim($record[$fieldToUse]);
                    break;
                }
            }
        } catch (InvalidArgumentException $e) {
            // Nothing to do.
        }
    }

    /**
     * Returns the limit to use to fetch records.
     *
     * @return integer
     */
    protected function getLimit(): int
    {
        // TODO: Make configurable.
        return 50;
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return array|null
     */
    abstract protected function getRecords(int $offset, int $limit);

    /**
     * @param integer $identifier
     * @return array
     */
    abstract protected function getRecord(int $identifier): array;

    /**
     * @return string
     */
    abstract protected function getDocumentName(): string;

    /**
     * @param string $identifier
     * @return string
     */
    abstract public function getIdentifier($identifier): string;
}
