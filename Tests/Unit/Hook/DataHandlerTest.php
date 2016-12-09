<?php
namespace Leonmrni\SearchCore\Tests\Unit\Hook;

/*
 * Copyright (C) 2016  Daniel Siepmann <daniel.siepmann@typo3.org>
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

use Leonmrni\SearchCore\Hook\DataHandler as Hook;
use Leonmrni\SearchCore\Service\DataHandler;
use Leonmrni\SearchCore\Tests\Fakes\FakeLogManager;
use TYPO3\CMS\Core\DataHandling\DataHandler as CoreDataHandler;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 *
 */
class DataHandlerTest extends UnitTestCase
{
    /**
     * @var DataHandler|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $subject;

    /**
     * @var Hook|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $hook;

    /**
     * Set up the tests
     */
    protected function setUp()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\Container\Container')
            ->registerImplementation(LogManager::class, FakeLogManager::class);

        $this->subject = $this->getAccessibleMock(DataHandler::class);
        $this->hook = $this->getAccessibleMock(
            Hook::class,
            [
                'getTablesToProcess',
                'getRecord'
            ],
            [
                $this->subject,
            ]
        );

        $this->hook->method('getTablesToProcess')
            ->willReturn(['table']);
        $this->hook->method('getRecord')
            ->willReturn([
                'title' => 'some title',
                'bodytext' => 'some text',
            ]);
    }

    /**
     * @test
     */
    public function notConfiguredTablesWillNotBeProcessed()
    {
        $table = 'noneConfiguredTable';
        $recordUid = 1;
        $this->subject->expects($this->exactly(0))->method('delete');
        $this->subject->expects($this->exactly(0))->method('add');
        $this->subject->expects($this->exactly(0))->method('update');

        $dataHandler = $this->getAccessibleMock(CoreDataHandler::class, [], [], '', false);
        $dataHandler->substNEWwithIDs = ['NEW34' => $recordUid];

        $this->hook->processCmdmap_deleteAction($table, $recordUid, [], false, $dataHandler);
        $this->hook->processDatamap_afterDatabaseOperations('new', $table, 'NEW34', [], $dataHandler);
        $this->hook->processDatamap_afterDatabaseOperations('update', $table, $recordUid, [], $dataHandler);
    }

    /**
     * @test
     */
    public function configuredTablesWillBeProcessed()
    {
        $table = 'table';
        $recordUid = 1;
        $this->subject->expects($this->once())->method('delete');
        $this->subject->expects($this->once())->method('add');
        $this->subject->expects($this->once())->method('update');

        $dataHandler = $this->getAccessibleMock(CoreDataHandler::class, [], [], '', false);
        $dataHandler->substNEWwithIDs = ['NEW34' => $recordUid];

        $this->hook->processCmdmap_deleteAction($table, $recordUid, [], false, $dataHandler);
        $this->hook->processDatamap_afterDatabaseOperations('new', $table, 'NEW34', [], $dataHandler);
        $this->hook->processDatamap_afterDatabaseOperations('update', $table, $recordUid, [], $dataHandler);
    }

    /**
     * @test
     */
    public function deletionWillBeTriggered()
    {
        $table = 'table';
        $recordUid = 1;
        $this->subject->expects($this->once())
            ->method('delete')
            ->with(
                $this->equalTo($table),
                $this->equalTo($recordUid)
            );

        $this->hook->processCmdmap_deleteAction($table, $recordUid, [], false, new CoreDataHandler());
    }

    /**
     * @test
     */
    public function updateWillBeTriggered()
    {
        $table = 'table';
        $recordUid = 1;
        $record = [
            'title' => 'some title',
            'bodytext' => 'some text',
        ];
        $this->subject->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo($table),
                $this->equalTo($recordUid),
                $this->equalTo($record)
            );

        $this->hook->processDatamap_afterDatabaseOperations('update', $table, $recordUid, $record, new CoreDataHandler);
    }
}
