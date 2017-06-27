<?php
namespace Leonmrni\SearchCore\Tests\Indexing;

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

use Leonmrni\SearchCore\Configuration\ConfigurationContainerInterface;
use Leonmrni\SearchCore\Connection\Elasticsearch;
use Leonmrni\SearchCore\Domain\Index\TcaIndexer;
use Leonmrni\SearchCore\Domain\Index\TcaIndexer\RelationResolver;
use Leonmrni\SearchCore\Domain\Index\TcaIndexer\TcaTableService;
use Leonmrni\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TcaIndexerTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     */
    public function respectRootLineBlacklist()
    {
        $this->importDataSet('Tests/Functional/Fixtures/Indexing/TcaIndexer/RespectRootLineBlacklist.xml');
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);
        $tableName = 'tt_content';
        $tableService = $objectManager->get(
            TcaTableService::class,
            $tableName,
            $objectManager->get(RelationResolver::class),
            $objectManager->get(ConfigurationContainerInterface::class)
        );

        $connection = $this->getMockBuilder(Elasticsearch::class)
            ->setMethods(['addDocuments'])
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->once())
            ->method('addDocuments')
            ->with(
                $this->stringContains('tt_content'),
                $this->callback(function ($documents) {
                    foreach ($documents as $document) {
                        // Page uids 1 and 2 are allowed while 3 and 4 are not allowed.
                        // Therefore only documents with page uid 1 and 2 should exist.
                        if (! isset($document['pid']) || ! in_array($document['pid'], [1, 2])) {
                            return false;
                        }
                    }

                    return true;
                })
            );

        $objectManager->get(TcaIndexer::class, $tableService, $connection)->indexAllDocuments();
    }

    protected function getTypoScriptFilesForFrontendRootPage()
    {
        return array_merge(
            parent::getTypoScriptFilesForFrontendRootPage(),
            ['EXT:search_core/Tests/Functional/Fixtures/Indexing/TcaIndexer/RespectRootLineBlacklist.ts']
        );
    }
}
