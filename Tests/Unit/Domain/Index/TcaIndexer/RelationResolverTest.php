<?php
namespace Codappix\SearchCore\Tests\Unit\Domain\Index\TcaIndexer;

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

use Codappix\SearchCore\Domain\Index\TcaIndexer\RelationResolver;
use Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableServiceInterface;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class RelationResolverTest extends AbstractUnitTestCase
{
    /**
     * @var RelationResolver
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = new RelationResolver();
    }

    /**
     * @test
     */
    public function renderTypeInputDateTimeIsHandled()
    {
        $originalRecord = [
            'starttime' => 0,
        ];
        $record = $originalRecord;
        $GLOBALS['TCA'] = [
            'tt_content' => [
                'columns' => [
                    'starttime' => [
                        'config' => [
                            'default' => 0,
                            'eval' => 'datetime',
                            'renderType' => 'inputDateTime',
                        ],
                        'type' => 'input',
                        'exclude' => 1,
                        'l10n_display' => 'defaultAsReadonly',
                        'l10n_mode' => 'exclude',
                        'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                    ],
                ],
            ],
        ];
        $tableServiceMock = $this->getMockBuilder(TcaTableServiceInterface::class)->getMock();
        $tableServiceMock->expects($this->any())
            ->method('getTableName')
            ->willReturn('tt_content');
        $tableServiceMock->expects($this->any())
            ->method('getColumnConfig')
            ->willReturn($GLOBALS['TCA']['tt_content']['columns']['starttime']['config']);

        $this->subject->resolveRelationsForRecord($tableServiceMock, $record);

        $this->assertSame(
            $originalRecord,
            $record,
            'TCA column configured with renderType inputDateTime was not kept as unix timestamp.'
        );
    }
}
