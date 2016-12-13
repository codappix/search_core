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
use Leonmrni\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 *
 */
class IndexTcaTableTest extends AbstractFunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->importDataSet('Tests/Functional/Fixtures/Indexing/IndexTcaTable.xml');
    }

    /**
     * @test
     */
    public function indexBasicTtContent()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->index()
            ;

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertTrue($response->isOK());
        $this->assertSame($response->getData()['hits']['total'], 1, 'Not exactly 1 document was indexed.');
        $this->assertArraySubset(
            ['_source' => ['header' => 'indexed content element']],
            $response->getData()['hits']['hits'][0],
            false,
            'Record was not indexed.'
        );
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

    /**
     * @test
     */
    public function indexingRespectsUserWhereClause()
    {
        $this->setUpFrontendRootPage(1, ['EXT:search_core/Tests/Functional/Fixtures/Indexing/UserWhereClause.ts']);
        $this->importDataSet('Tests/Functional/Fixtures/Indexing/UserWhereClause.xml');

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->index()
            ;

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertTrue($response->isOK());
        $this->assertSame($response->getData()['hits']['total'], 2, 'Not exactly 2 document was indexed.');
        $this->assertArraySubset(
            ['_source' => ['header' => 'Also indexable record']],
            $response->getData()['hits']['hits'][0],
            false,
            'Record was not indexed.'
        );
        $this->assertArraySubset(
            ['_source' => ['header' => 'indexed content element']],
            $response->getData()['hits']['hits'][1],
            false,
            'Record was not indexed.'
        );
    }

    /**
     * @test
     */
    public function resolvesRelations()
    {
        $this->importDataSet('Tests/Functional/Fixtures/Indexing/ResolveRelations.xml');

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->index()
            ;

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertArraySubset(
            ['_source' => [
                'uid' => '9',
                'CType' => 'textmedia', // Testing items
                'categories' => ['Category 2', 'Category 1'], // Testing mm (with sorting)
            ]],
            $response->getData()['hits']['hits'][0],
            false,
            'Record was not indexed with resolved category relation to a single value.'
        );
        $this->assertArraySubset(
            ['_source' => [
                'uid' => '10',
                'CType' => 'textmedia',
                'categories' => ['Category 2'],
            ]],
            $response->getData()['hits']['hits'][1],
            false,
            'Record was not indexed with resolved category relation to multiple values.'
        );
        $this->assertArraySubset(
            ['_source' => [
                'uid' => '6',
                'categories' => null,
            ]],
            $response->getData()['hits']['hits'][2],
            false,
            'Record was indexed with resolved category relation, but should not have any.'
        );
    }
}
