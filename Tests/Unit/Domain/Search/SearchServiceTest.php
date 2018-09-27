<?php
namespace Codappix\SearchCore\Tests\Unit\Domain\Search;

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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Configuration\InvalidArgumentException;
use Codappix\SearchCore\Connection\ConnectionInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use Codappix\SearchCore\DataProcessing\Service as DataProcessorService;
use Codappix\SearchCore\Domain\Model\SearchRequest;
use Codappix\SearchCore\Domain\Model\SearchResult;
use Codappix\SearchCore\Domain\Search\SearchService;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class SearchServiceTest extends AbstractUnitTestCase
{
    /**
     * @var SearchService
     */
    protected $subject;

    /**
     * @var SearchResultInterface
     */
    protected $result;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var DataProcessorService
     */
    protected $dataProcessorService;

    public function setUp()
    {
        parent::setUp();

        $this->result = $this->getMockBuilder(SearchResultInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->connection = $this->getMockBuilder(ConnectionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProcessorService = $this->getMockBuilder(DataProcessorService::class)
            ->setConstructorArgs([$this->objectManager])
            ->getMock();

        $this->subject = new SearchService(
            $this->connection,
            $this->configuration,
            $this->objectManager,
            $this->dataProcessorService
        );
    }

    /**
     * @test
     */
    public function sizeIsAddedFromConfiguration()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(45, null));
            $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));
        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getLimit() === 45;
            }))
            ->willReturn($this->getMockBuilder(SearchResultInterface::class)->getMock());

        $searchRequest = new SearchRequest('SearchWord');
        $this->subject->search($searchRequest);
    }

    /**
     * @test
     */
    public function defaultSizeIsAddedIfNothingIsConfigured()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));
        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getLimit() === 10;
            }))
            ->willReturn($this->getMockBuilder(SearchResultInterface::class)->getMock());

        $searchRequest = new SearchRequest('SearchWord');
        $this->subject->search($searchRequest);
    }

    /**
     * @test
     */
    public function configuredFilterAreAddedToRequestWithoutAnyFilter()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->onConsecutiveCalls(
                ['property' => 'something'],
                $this->throwException(new InvalidArgumentException)
            ));

        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getFilter() === ['property' => 'something'];
            }))
            ->willReturn($this->getMockBuilder(SearchResultInterface::class)->getMock());

        $searchRequest = new SearchRequest('SearchWord');
        $this->subject->search($searchRequest);
    }

    /**
     * @test
     */
    public function configuredFilterWithValueZeroAreAddedToRequestWithoutAnyFilter()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->onConsecutiveCalls(
                ['property' => '0'],
                $this->throwException(new InvalidArgumentException)
            ));

        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getFilter() === ['property' => '0'];
            }))
            ->willReturn($this->getMockBuilder(SearchResultInterface::class)->getMock());

        $searchRequest = new SearchRequest('SearchWord');
        $this->subject->search($searchRequest);
    }

    /**
     * @test
     */
    public function configuredFilterAreAddedToRequestWithExistingFilter()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->onConsecutiveCalls(
                ['property' => 'something'],
                $this->throwException(new InvalidArgumentException)
            ));

        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getFilter() === [
                    'anotherProperty' => 'anything',
                    'property' => 'something',
                ];
            }))
            ->willReturn($this->getMockBuilder(SearchResultInterface::class)->getMock());

        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->setFilter(['anotherProperty' => 'anything']);
        $this->subject->search($searchRequest);
    }

    /**
     * @test
     */
    public function nonConfiguredFilterIsNotChangingRequestWithExistingFilter()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));

        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getFilter() === ['anotherProperty' => 'anything'];
            }))
            ->willReturn($this->getMockBuilder(SearchResultInterface::class)->getMock());

        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->setFilter(['anotherProperty' => 'anything']);
        $this->subject->search($searchRequest);
    }

    /**
     * @test
     */
    public function originalSearchResultIsReturnedIfNoDataProcessorIsConfigured()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));

        $searchResultMock = $this->getMockBuilder(SearchResultInterface::class)->getMock();

        $this->connection->expects($this->once())
            ->method('search')
            ->willReturn($searchResultMock);

        $this->dataProcessorService->expects($this->never())->method('executeDataProcessor');

        $searchRequest = new SearchRequest('');
        $this->assertSame(
            $searchResultMock,
            $this->subject->search($searchRequest),
            'Did not get created result without applied data processing'
        );
    }

    /**
     * @test
     */
    public function configuredDataProcessorsAreExecutedOnSearchResult()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->onConsecutiveCalls(
                $this->throwException(new InvalidArgumentException),
                ['SomeProcessorClass']
            ));

        $searchResultMock = $this->getMockBuilder(SearchResultInterface::class)->getMock();
        $searchResult = new SearchResult($searchResultMock, [
            [
                'data' => [
                    'field 1' => 'value 1'
                ],
                'type' => 'testType',
            ],
        ]);

        $this->connection->expects($this->once())
            ->method('search')
            ->willReturn($searchResult);

        $this->dataProcessorService->expects($this->once())
            ->method('executeDataProcessor')
            ->with('SomeProcessorClass', ['field 1' => 'value 1'])
            ->willReturn([
                'field 1' => 'value 1',
                'field 2' => 'value 2',
            ]);

        $this->objectManager->expects($this->once())
            ->method('get')
            ->with(SearchResult::class, $searchResult, [
                [
                    'data' => [
                        'field 1' => 'value 1',
                        'field 2' => 'value 2',
                    ],
                    'type' => 'testType',
                ]
            ])
            ->willReturn($searchResultMock);

        $searchRequest = new SearchRequest('');
        $this->assertSame(
            $searchResultMock,
            $this->subject->search($searchRequest),
            'Did not get created result with applied data processing'
        );
    }
}
