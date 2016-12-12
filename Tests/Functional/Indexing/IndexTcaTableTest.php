<?php
namespace Leonmrni\SearchCore\Tests\Functional\Indexing;

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

use Leonmrni\SearchCore\Domain\Index\IndexerFactory;
use Leonmrni\SearchCore\Tests\Functional\FunctionalTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 *
 */
class IndexTcaTableTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->importDataSet('Tests/Functional/Fixtures/Indexing/IndexTcaTable.xml');
    }

    /**
     * @test
     */
    public function indexBasicTtContentWithoutBasicConfiguration()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->index()
            ;

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertTrue($response->isOK());
        $this->assertSame($response->getData()['hits']['total'], 1, 'Not exactly 1 document was indexed.');
    }

    /**
     * @test
     * @expectedException \Leonmrni\SearchCore\Domain\Index\IndexingException
     */
    public function indexingNonConfiguredTableWillThrowException()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('non_existing_table')
            ;
    }

    /**
     * @test
     */
    public function canHandleExisingIndex()
    {
        $indexer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ;

        $indexer->index();

        // Index 2nd time, index already exists in elasticsearch.
        $indexer->index();

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertTrue($response->isOK());
        $this->assertSame($response->getData()['hits']['total'], 1, 'Not exactly 1 document was indexed.');
    }
}
