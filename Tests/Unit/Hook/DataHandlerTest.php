<?php

namespace Codappix\SearchCore\Tests\Unit\Hook;

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

use Codappix\SearchCore\Domain\Service\DataHandler as OwnDataHandler;
use Codappix\SearchCore\Hook\DataHandler;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\DataHandling\DataHandler as CoreDataHandler;

class DataHandlerToProcessorTest extends AbstractUnitTestCase
{
    /**
     * @test
     * @dataProvider getPossibleCallCombinations
     * @param array $parameters
     * @param bool $expectCall
     */
    public function fieldsAreCopiedAsConfigured(array $parameters, bool $expectCall)
    {
        $coreDataHandlerMock = $this->getMockBuilder(CoreDataHandler::class)->getMock();
        $ownDataHandlerMock = $this->getMockBuilder(OwnDataHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $subject = $this->getMockBuilder(DataHandler::class)
            ->setConstructorArgs([$ownDataHandlerMock])
            ->setMethods(['getRecord'])
            ->getMock();

        $ownDataHandlerMock->expects($this->any())
            ->method('supportsTable')
            ->willReturn(true);

        if ($expectCall) {
            $subject->expects($this->once())
                ->method('getRecord')
                ->with('pages', 10)
                ->willReturn(['uid' => 10]);
            $ownDataHandlerMock->expects($this->once())
                ->method('update')
                ->with('pages', ['uid' => 10]);
        } else {
            $subject->expects($this->never())
                ->method('getRecord');
            $ownDataHandlerMock->expects($this->never())
                ->method('update');
        }

        $subject->clearCachePostProc($parameters, $coreDataHandlerMock);
    }

    public function getPossibleCallCombinations(): array
    {
        return [
            'Editor triggered cache clear of page manual' => [
                'parameters' => [
                    'cacheCmd' => '10',
                ],
                'expectCall' => true,
            ],
            'Editor changed records on a page' => [
                'parameters' => [
                    'uid_page' => 10,
                ],
                'expectCall' => true,
            ],
            'Something unexpected' => [
                'parameters' => [],
                'expectCall' => false,
            ],
            'Something unexpected' => [
                'parameters' => [
                    'cacheCmd' => 'something like a tag?!',
                ],
                'expectCall' => false,
            ],
        ];
    }

    /**
     * @test
     */
    public function indexingIsNotCalledForCacheClearIfDataIsInvalid()
    {
        $coreDataHandlerMock = $this->getMockBuilder(CoreDataHandler::class)->getMock();
        $ownDataHandlerMock = $this->getMockBuilder(OwnDataHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new DataHandler($ownDataHandlerMock);

        $ownDataHandlerMock->expects($this->never())->method('update');

        $subject->clearCachePostProc([
            'cacheCmd' => 'NEW343',
        ], $coreDataHandlerMock);
    }

    /**
     * @test
     */
    public function indexingIsNotCalledForProcessIfDataIsInvalid()
    {
        $coreDataHandlerMock = $this->getMockBuilder(CoreDataHandler::class)->getMock();
        $coreDataHandlerMock->datamap = [
            'tt_content' => [
                'NEW343' => [],
            ],
        ];
        $coreDataHandlerMock->substNEWwithIDs = [];

        $ownDataHandlerMock = $this->getMockBuilder(OwnDataHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new DataHandler($ownDataHandlerMock);

        $ownDataHandlerMock->expects($this->never())->method('update');

        $subject->processDatamap_afterAllOperations($coreDataHandlerMock);
    }
}
