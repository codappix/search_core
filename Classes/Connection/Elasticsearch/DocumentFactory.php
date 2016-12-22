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
     * @var MapperFactory
     */
    protected $mapperFactory;

    public function __construct(\TYPO3\CMS\Core\Log\LogManager $logManager, MapperFactory $mapperFactory)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * Creates document from document.
     *
     * @param string $documentType
     * @param array $document
     *
     * @return \Elastica\Document
     */
    public function getDocument($documentType, array $document)
    {
        if (!isset($document['search_identifier'])) {
             throw new \Exception('No search_identifier provided for document.', 1481194385);
        }

        $identifier = $document['search_identifier'];
        unset($document['search_identifier']);

        $this->mapperFactory->getMapper($documentType)->applyMappingToDocument($document);

        $this->logger->debug('Convert document to document', [$identifier, $document]);
        return new \Elastica\Document($identifier, $document);
    }

    /**
     * Creates documents based on documents.
     *
     * @param string $documentType
     * @param array $documents
     *
     * @return array
     */
    public function getDocuments($documentType, array $documents)
    {
        foreach ($documents as &$document) {
            $document = $this->getDocument($documentType, $document);
        }

        return $documents;
    }
}
