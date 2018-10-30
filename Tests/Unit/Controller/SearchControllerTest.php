<?php

namespace Codappix\Tests\Unit\Controller;

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

use Codappix\SearchCore\Controller\SearchController;
use Codappix\SearchCore\Domain\Model\SearchRequest;
use Codappix\SearchCore\Domain\Search\CachedSearchService;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class SearchControllerTest extends AbstractUnitTestCase
{
    /**
     * @var SearchController
     */
    protected $subject;

    /**
     * @var Request
     */
    protected $request;

    public function setUp()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Cache\CacheManager::class
        )->setCacheConfigurations([
            'extbase_object' => [
                'backend' => \TYPO3\CMS\Core\Cache\Backend\NullBackend::class,
            ],
            'extbase_datamapfactory_datamap' => [
                'backend' => \TYPO3\CMS\Core\Cache\Backend\NullBackend::class,
            ],
        ]);

        parent::setUp();

        $searchService = $this->getMockBuilder(CachedSearchService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = new Request();

        $this->subject = new SearchController($searchService);
        $this->inject($this->subject, 'request', $this->request);
        $this->inject($this->subject, 'objectManager', new ObjectManager());
    }

    /**
     * @test
     */
    public function searchRequestArgumentIsAddedIfModeIsFilterAndArgumentDoesNotExist()
    {
        $this->inject($this->subject, 'settings', [
            'searching' => [
                'mode' => 'filter',
            ]
        ]);

        $this->subject->initializeResultsAction();
        $this->assertInstanceOf(
            SearchRequest::class,
            $this->request->getArgument('searchRequest'),
            'Search request was not created.'
        );
    }

    /**
     * @test
     */
    public function searchRequestArgumentIsAddedToExistingArguments()
    {
        $this->request->setArguments([
            '@widget_0' => [
                'currentPage' => '7',
            ]
        ]);
        $this->inject($this->subject, 'settings', [
            'searching' => [
                'mode' => 'filter',
            ]
        ]);

        $this->subject->initializeResultsAction();
        $this->assertInstanceOf(
            SearchRequest::class,
            $this->request->getArgument('searchRequest'),
            'Search request was not created.'
        );
        $this->assertSame(
            ['currentPage' => '7'],
            $this->request->getArgument('@widget_0'),
            'Existing arguments were not kept.'
        );
    }

    /**
     * @test
     */
    public function searchRequestArgumentIsNotAddedIfModeIsNotFilter()
    {
        $this->inject($this->subject, 'settings', ['searching' => []]);

        $this->subject->initializeResultsAction();
        $this->assertFalse(
            $this->request->hasArgument('searchRequest'),
            'Search request should not exist.'
        );
    }
}
