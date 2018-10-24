<?php
namespace Codappix\SearchCore\Tests\Functional\Hooks\DataHandler;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
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
use Codappix\SearchCore\Domain\Service\DataHandler as DataHandlerService;
use Codappix\SearchCore\Hook\DataHandler as DataHandlerHook;
use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ProcessesAllowedTablesTest extends AbstractDataHandlerTest
{
    /**
     * @var DataHandlerService|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function getDataSets()
    {
        return array_merge(
            parent::getDataSets(),
            ['EXT:search_core/Tests/Functional/Fixtures/Hooks/DataHandler/AllowedTables.xml']
        );
    }

    /**
     * @test
     */
    public function deletionWillBeTriggeredForTtContent()
    {
        $this->subject->expects($this->exactly(1))
            ->method('delete')
            ->with($this->equalTo('tt_content'), $this->equalTo('1'));
        $this->subject->expects($this->exactly(1))
            ->method('update')
            ->with('pages', $this->callback(function (array $record) {
                if ($this->isLegacyVersion()) {
                    return isset($record['uid']) && $record['uid'] === '1';
                } else {
                    return isset($record['uid']) && $record['uid'] === 1;
                }
            }));

        $tce = GeneralUtility::makeInstance(Typo3DataHandler::class);
        $tce->stripslashes_values = 0;
        $tce->start([], [
            'tt_content' => [
                '1' => [
                    'delete' => 1,
                ],
            ],
        ]);
        $tce->process_cmdmap();
    }

    /**
     * @test
     */
    public function updateWillBeTriggeredForExistingTtContent()
    {
        $this->subject->expects($this->exactly(2))->method('update')
            ->withConsecutive(
                [
                    $this->equalTo('tt_content'),
                    $this->callback(function ($record) {
                        if ($this->isLegacyVersion()) {
                            return isset($record['uid']) && $record['uid'] === '1'
                                && isset($record['pid']) && $record['pid'] === '1'
                                && isset($record['colPos']) && $record['colPos'] === '1'
                                ;
                        }

                        return isset($record['uid']) && $record['uid'] === 1
                            && isset($record['pid']) && $record['pid'] === 1
                            && isset($record['colPos']) && $record['colPos'] === 1
                            ;
                    })
                ],
                [
                    $this->equalTo('pages'),
                    $this->callback(function ($record) {
                        if ($this->isLegacyVersion()) {
                            return isset($record['uid']) && $record['uid'] === '1';
                        } else {
                            return isset($record['uid']) && $record['uid'] === 1;
                        }
                    })
                ]
            );

        $tce = GeneralUtility::makeInstance(Typo3DataHandler::class);
        $tce->stripslashes_values = 0;
        $tce->start([
            'tt_content' => [
                '1' => [
                    'colPos' => 1,
                ],
            ],
        ], []);
        $tce->process_datamap();
    }

    /**
     * @test
     */
    public function updateWillBeTriggeredForNewTtContent()
    {
        $this->subject->expects($this->exactly(2))->method('update')
            ->withConsecutive(
                [
                    $this->equalTo('tt_content'),
                    $this->callback(function ($record) {
                        if ($this->isLegacyVersion()) {
                            return isset($record['uid']) && $record['uid'] === '2'
                                && isset($record['pid']) && $record['pid'] === '1'
                                && isset($record['header']) && $record['header'] === 'a new record'
                                ;
                        }

                        return isset($record['uid']) && $record['uid'] === 2
                            && isset($record['pid']) && $record['pid'] === 1
                            && isset($record['header']) && $record['header'] === 'a new record'
                            ;
                    })
                ],
                [
                    $this->equalTo('pages'),
                    $this->callback(function ($record) {
                        if ($this->isLegacyVersion()) {
                            return isset($record['uid']) && $record['uid'] === '1';
                        } else {
                            return isset($record['uid']) && $record['uid'] === 1;
                        }
                    })
                ]
            );

        $tce = GeneralUtility::makeInstance(Typo3DataHandler::class);
        $tce->stripslashes_values = 0;
        $tce->start([
            'tt_content' => [
                'NEW_1' => [
                    'pid' => 1,
                    'header' => 'a new record',
                ],
            ],
        ], []);

        $tce->process_datamap();
    }
}
