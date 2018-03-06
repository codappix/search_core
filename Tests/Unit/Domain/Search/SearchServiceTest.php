<?php
namespace Copyright\SearchCore\Tests\Unit\Domain\Search;

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
        $this->connection->expects($this->any())
            ->method('search')
            ->willReturn($this->result);
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
            }));

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
            }));

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
            }));

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
            }));

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
            }));

        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->setFilter(['anotherProperty' => 'anything']);
        $this->subject->search($searchRequest);
    }

    /**
     * @test
     */
    public function emptyConfiguredFilterIsNotChangingRequestWithExistingFilter()
    {
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->onConsecutiveCalls(
                ['anotherProperty' => ''],
                $this->throwException(new InvalidArgumentException)
            ));

        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getFilter() === ['anotherProperty' => 'anything'];
            }));

        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->setFilter(['anotherProperty' => 'anything']);
        $this->subject->search($searchRequest);
    }
}
