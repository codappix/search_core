<?php
namespace Codappix\SearchCore\Tests\Unit\Domain\Model;

/*
 * Copyright (C) 2018  Daniel Siepmann <coding@daniel-siepmann.de>
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

use Codappix\SearchCore\Connection\ResultItemInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use Codappix\SearchCore\Domain\Model\SearchResult;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class SearchResultTest extends AbstractUnitTestCase
{
    /**
     * @test
     */
    public function countIsRetrievedFromOriginalResult()
    {
        $originalSearchResultMock = $this->getMockBuilder(SearchResultInterface::class)->getMock();
        $originalSearchResultMock->expects($this->once())->method('count');

        $subject = new SearchResult($originalSearchResultMock, []);
        $subject->count();
    }

    /**
     * @test
     */
    public function currentCountIsRetrievedFromOriginalResult()
    {
        $originalSearchResultMock = $this->getMockBuilder(SearchResultInterface::class)->getMock();
        $originalSearchResultMock->expects($this->once())->method('getCurrentCount');

        $subject = new SearchResult($originalSearchResultMock, []);
        $subject->getCurrentCount();
    }

    /**
     * @test
     */
    public function facetsAreRetrievedFromOriginalResult()
    {
        $originalSearchResultMock = $this->getMockBuilder(SearchResultInterface::class)->getMock();
        $originalSearchResultMock->expects($this->once())->method('getFacets');

        $subject = new SearchResult($originalSearchResultMock, []);
        $subject->getFacets();
    }

    /**
     * @test
     */
    public function resultItemsCanBeRetrieved()
    {
        $originalSearchResultMock = $this->getMockBuilder(SearchResultInterface::class)->getMock();
        $data = [
            [
                'uid' => 10,
                'title' => 'Some Title',
            ],
            [
                'uid' => 11,
                'title' => 'Some Title 2',
            ],
            [
                'uid' => 12,
                'title' => 'Some Title 3',
            ],
        ];

        $subject = new SearchResult($originalSearchResultMock, $data);
        $resultItems = $subject->getResults();

        $this->assertCount(3, $resultItems);

        $this->assertSame($data[0]['uid'], $resultItems[0]['uid']);
        $this->assertSame($data[1]['uid'], $resultItems[1]['uid']);
        $this->assertSame($data[2]['uid'], $resultItems[2]['uid']);

        $this->assertInstanceOf(ResultItemInterface::class, $resultItems[0]);
        $this->assertInstanceOf(ResultItemInterface::class, $resultItems[1]);
        $this->assertInstanceOf(ResultItemInterface::class, $resultItems[2]);
    }
}
