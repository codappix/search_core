<?php
namespace Codappix\SearchCore\Tests\Indexing;

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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Connection\Elasticsearch;
use Codappix\SearchCore\Domain\Index\IndexerFactory;
use Codappix\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class PagesIndexerTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     */
    public function pagesContainAllAdditionalInformation()
    {
        $this->importDataSet('Tests/Functional/Fixtures/Indexing/IndexTcaTable.xml');

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);
        $tableName = 'pages';

        $connection = $this->getMockBuilder(Elasticsearch::class)
            ->setMethods(['addDocuments'])
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->once())
            ->method('addDocuments')
            ->with(
                $this->stringContains($tableName),
                $this->callback(function ($documents) {
                    return count($documents) === 2
                        && isset($documents[0]['content']) && $documents[0]['content'] ===
                        'indexed content element' .
                        ' this is the content of header content element that should get indexed' .
                        ' Indexed without html tags Some text in paragraph'
                        && isset($documents[0]['search_abstract']) && $documents[0]['search_abstract'] ===
                        'Used as abstract as no abstract is defined.'
                        ;
                })
            );

        $indexer = $objectManager->get(IndexerFactory::class)->getIndexer($tableName);
        $this->inject($indexer, 'connection', $connection);
        $indexer->indexAllDocuments();
    }

    /**
     * @test
     * @dataProvider rootLineDataSets
     * @param string $dataSetPath
     * @throws \Codappix\SearchCore\Domain\Index\NoMatchingIndexerException
     * @throws \TYPO3\TestingFramework\Core\Exception
     */
    public function rootLineIsRespectedDuringIndexing($dataSetPath)
    {
        $this->importDataSet($dataSetPath);

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);
        $tableName = 'pages';

        $connection = $this->getMockBuilder(Elasticsearch::class)
            ->setMethods(['addDocuments'])
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->once())
            ->method('addDocuments')
            ->with(
                $this->stringContains($tableName),
                $this->callback(function ($documents) {
                    return count($documents) === 2;
                })
            );

        $indexer = $objectManager->get(IndexerFactory::class)->getIndexer($tableName);
        $this->inject($indexer, 'connection', $connection);
        $indexer->indexAllDocuments();
    }

    public function rootLineDataSets()
    {
        return [
            'Broken root line' => ['Tests/Functional/Fixtures/Indexing/PagesIndexer/BrokenRootLine.xml'],
            'Recycler doktype' => ['Tests/Functional/Fixtures/Indexing/PagesIndexer/Recycler.xml'],
            'Extended timing to sub pages' => ['Tests/Functional/Fixtures/Indexing/PagesIndexer/InheritedTiming.xml'],
        ];
    }
}
