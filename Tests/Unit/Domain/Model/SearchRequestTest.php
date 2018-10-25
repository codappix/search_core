<?php

namespace Codappix\SearchCore\Tests\Unit\Domain\Model;

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

use Codappix\SearchCore\Connection\ConnectionInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use Codappix\SearchCore\Domain\Model\SearchRequest;
use Codappix\SearchCore\Domain\Search\SearchService;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class SearchRequestTest extends AbstractUnitTestCase
{
    /**
     * @test
     * @dataProvider possibleEmptyFilter
     * @param array $filter
     */
    public function emptyFilterWillNotBeSet(array $filter)
    {
        $subject = new SearchRequest();
        $subject->setFilter($filter);

        $this->assertSame(
            [],
            $subject->getFilter(),
            'Empty filter were set, even if they should not.'
        );
    }

    /**
     * Data provider for emptyFilterWillNotBeSet()
     * @return array
     */
    public function possibleEmptyFilter()
    {
        return [
            'Complete empty Filter' => [
                'filter' => [],
            ],
            'Single filter with empty value' => [
                'filter' => [
                    'someFilter' => '',
                ],
            ],
            'Single filter with empty recursive values' => [
                'filter' => [
                    'someFilter' => [
                        'someKey' => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function filterIsSet()
    {
        $filter = ['someField' => 'someValue'];
        $subject = new SearchRequest();
        $subject->setFilter($filter);

        $this->assertSame(
            $filter,
            $subject->getFilter(),
            'Filter was not set.'
        );
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfSearchServiceWasNotSet()
    {
        $subject = new SearchRequest();
        $subject->setConnection($this->getMockBuilder(ConnectionInterface::class)->getMock());
        $this->expectException(\InvalidArgumentException::class);
        $subject->execute();
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfConnectionWasNotSet()
    {
        $subject = new SearchRequest();
        $subject->setSearchService(
            $this->getMockBuilder(SearchService::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
        $this->expectException(\InvalidArgumentException::class);
        $subject->execute();
    }

    /**
     * @test
     */
    public function executionMakesUseOfProvidedConnectionAndSearchService()
    {
        $searchServiceMock = $this->getMockBuilder(SearchService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();
        $searchResultMock = $this->getMockBuilder(SearchResultInterface::class)
            ->getMock();

        $subject = new SearchRequest();
        $subject->setSearchService($searchServiceMock);
        $subject->setConnection($connectionMock);

        $connectionMock->expects($this->once())
            ->method('search')
            ->with($subject)
            ->willReturn($searchResultMock);
        $searchServiceMock->expects($this->once())
            ->method('processResult')
            ->with($searchResultMock);

        $subject->execute();
    }
}
