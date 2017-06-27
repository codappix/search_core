<?php
namespace Leonmrni\SearchCore\Tests\Functional\Connection\Elasticsearch;

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
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * TODO: https://github.com/DanielSiepmann/search_core/issues/16
 */
class IndexTcaTableTest extends AbstractFunctionalTestCase
{
    protected function getDataSets()
    {
        return array_merge(
            parent::getDataSets(),
            ['Tests/Functional/Fixtures/Indexing/IndexTcaTable.xml']
        );
    }

    /**
     * @test
     */
    public function indexBasicTtContent()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->indexAllDocuments()
            ;

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertTrue($response->isOK(), 'Elastica did not answer with ok code.');
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
     * TODO: this does not test the indexer, it tests the backend
     * @test
     */
    public function canHandleExistingIndex()
    {
        $indexer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ;

        $indexer->indexAllDocuments();

        // Index 2nd time, index already exists in elasticsearch.
        $indexer->indexAllDocuments();

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertTrue($response->isOK(), 'Elastica did not answer with ok code.');
        $this->assertSame($response->getData()['hits']['total'], 1, 'Not exactly 1 document was indexed.');
    }

    /**
     * @test
     */
    public function indexingRespectsUserWhereClause()
    {
        $this->setUpFrontendRootPage(1, array_merge(
            parent::getTypoScriptFilesForFrontendRootPage(),
            ['EXT:search_core/Tests/Functional/Fixtures/Indexing/UserWhereClause.ts']
        ));
        $this->importDataSet('Tests/Functional/Fixtures/Indexing/UserWhereClause.xml');

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->indexAllDocuments()
            ;

        $response = $this->client->request('typo3content/_search?q=*:*');

        $this->assertTrue($response->isOK(), 'Elastica did not answer with ok code.');
        $this->assertSame($response->getData()['hits']['total'], 2, 'Not exactly 2 documents were indexed.');
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
            ->indexAllDocuments()
            ;

        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertTrue($response->isOK(), 'Elastica did not answer with ok code.');
        $this->assertSame($response->getData()['hits']['total'], 3, 'Not exactly 3 documents were indexed.');

        $response = $this->client->request('typo3content/_search?q=uid:9');
        $this->assertArraySubset(
            ['_source' => [
                'uid' => '9',
                'CType' => 'Header', // Testing items
                'categories' => ['Category 2', 'Category 1'], // Testing mm (with sorting)
            ]],
            $response->getData()['hits']['hits'][0],
            false,
            'Record was not indexed with resolved category relations to multiple values.'
        );

        $response = $this->client->request('typo3content/_search?q=uid:10');
        $this->assertArraySubset(
            ['_source' => [
                'uid' => '10',
                'CType' => 'Header',
                'categories' => ['Category 2'],
            ]],
            $response->getData()['hits']['hits'][0],
            false,
            'Record was not indexed with resolved category relations to a single value.'
        );

        $response = $this->client->request('typo3content/_search?q=uid:6');
        $this->assertArraySubset(
            ['_source' => [
                'uid' => '6',
                'categories' => null,
            ]],
            $response->getData()['hits']['hits'][0],
            false,
            'Record was indexed with resolved category relation, but should not have any.'
        );
    }
}
