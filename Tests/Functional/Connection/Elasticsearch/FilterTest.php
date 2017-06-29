<?php
namespace Leonmrni\SearchCore\Tests\Functional\Connection\Elasticsearch;

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

use Leonmrni\SearchCore\Domain\Index\IndexerFactory;
use Leonmrni\SearchCore\Domain\Model\SearchRequest;
use Leonmrni\SearchCore\Domain\Search\SearchService;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class FilterTest extends AbstractFunctionalTestCase
{
    protected function getDataSets()
    {
        return array_merge(
            parent::getDataSets(),
            ['Tests/Functional/Fixtures/Searching/Filter.xml']
        );
    }

    /**
     * @test
     */
    public function itsPossibleToFilterResultsByASingleField()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->indexAllDocuments()
            ;

        $searchService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(SearchService::class);
        $searchRequest = new SearchRequest('Search Word');

        $result = $searchService->search($searchRequest);
        $this->assertSame(2, count($result), 'Did not receive both indexed elements without filter.');

        $searchRequest->setFilter(['CType' => 'html']);
        $result = $searchService->search($searchRequest);
        $this->assertSame('5', $result[0]->getData()['uid'], 'Did not get the expected result entry.');
        $this->assertSame(1, count($result), 'Did not receive the single filtered element.');
    }
}
