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
use Codappix\SearchCore\Connection\ConnectionInterface;
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

    public function setUp()
    {
        parent::setUp();

        $this->connection = $this->getMockBuilder(ConnectionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = new SearchService(
            $this->connection,
            $this->configuration,
            $this->objectManager
        );
    }

    /**
     * @test
     */
    public function sizeIsAddedFromConfiguration()
    {
        $this->configuration->expects($this->exactly(2))
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(45, null));
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
        $this->configuration->expects($this->exactly(2))
            ->method('getIfExists')
            ->withConsecutive(['searching.size'], ['searching.facets'])
            ->will($this->onConsecutiveCalls(null, null));
        $this->connection->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($searchRequest) {
                return $searchRequest->getLimit() === 10;
            }));

        $searchRequest = new SearchRequest('SearchWord');
        $this->subject->search($searchRequest);
    }
}
