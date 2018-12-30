<?php

namespace Codappix\SearchCore\Tests\Functional\Connection\Elasticsearch;

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
use TYPO3\CMS\Extbase\Object\ObjectManager;

class IndexDeletionTest extends AbstractFunctionalTestCase
{
    /**
     * @return array
     */
    protected function getDataSets()
    {
        return array_merge(
            parent::getDataSets(),
            ['EXT:search_core/Tests/Functional/Fixtures/Indexing/IndexDeletion.xml']
        );
    }

    /**
     * @test
     */
    public function indexIsDeleted()
    {
        $this->client->getIndex('typo3content')->create();
        $this->assertTrue(
            $this->client->getIndex('typo3content')->exists(),
            'Could not create index for test.'
        );

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->delete();

        $this->assertFalse(
            $this->client->getIndex('typo3content')->exists(),
            'Index could not be deleted through command controller.'
        );
    }

    /**
     * @test
     */
    public function documentsAreDeleted()
    {
        $index = $this->client->getIndex('typo3content');
        $index->create();
        $this->assertTrue(
            $index->exists(),
            'Could not create index for test.'
        );

        $contentIndexer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content');
        $pageIndexer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('pages');

        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertSame($response->getData()['hits']['total'], 0, 'Index should be empty.');

        $contentIndexer->indexAllDocuments();
        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertSame($response->getData()['hits']['total'], 3, 'Not exactly 3 documents are in index.');

        $pageIndexer->indexAllDocuments();
        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertSame($response->getData()['hits']['total'], 5, 'Not exactly 5 documents are in index.');

        $contentIndexer->deleteAllDocuments();
        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertSame($response->getData()['hits']['total'], 2, 'Not exactly 2 documents are in index.');

        $pageIndexer->deleteAllDocuments();
        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertSame($response->getData()['hits']['total'], 0, 'Index should be empty.');
    }
}
