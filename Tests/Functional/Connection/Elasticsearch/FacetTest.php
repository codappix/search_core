<?php
namespace Codappix\SearchCore\Tests\Functional\Connection\Elasticsearch;

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

use Codappix\SearchCore\Domain\Index\IndexerFactory;
use Codappix\SearchCore\Domain\Model\SearchRequest;
use Codappix\SearchCore\Domain\Search\SearchService;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class FacetTest extends AbstractFunctionalTestCase
{
    protected function getTypoScriptFilesForFrontendRootPage()
    {
        return array_merge(
            parent::getTypoScriptFilesForFrontendRootPage(),
            ['EXT:search_core/Tests/Functional/Fixtures/Searching/Facet.ts']
        );
    }

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
    public function itsPossibleToFetchFacetsForField()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->indexAllDocuments()
            ;

        $searchService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(SearchService::class);

        $searchRequest = new SearchRequest();
        $result = $searchService->search($searchRequest);

        $this->assertSame(1, count($result->getFacets()), 'Did not receive the single defined facet.');

        $facet = current($result->getFacets());
        $this->assertSame('contentTypes', $facet->getName(), 'Name of facet was not as expected.');
        $this->assertSame('CType', $facet->getField(), 'Field of facet was not expected.');

        $options = $facet->getOptions();
        $this->assertSame(2, count($options), 'Did not receive the expected number of possible options for facet.');
        $option = $options['HTML'];
        $this->assertSame('HTML', $option->getName(), 'Option did not have expected Name.');
        $this->assertSame(1, $option->getCount(), 'Option did not have expected count.');
        $option = $options['Header'];
        $this->assertSame('Header', $option->getName(), 'Option did not have expected Name.');
        $this->assertSame(1, $option->getCount(), 'Option did not have expected count.');
    }
}
