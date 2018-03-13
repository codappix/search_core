<?php
namespace Codappix\SearchCore\Tests\Unit\Domain\Index\TcaIndexer;

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
use Codappix\SearchCore\DataProcessing\CopyToProcessor;
use Codappix\SearchCore\Domain\Index\TcaIndexer\RelationResolver;
use Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableService76;
use Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableService;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\Database\DatabaseConnection;

class TcaTableServiceTest extends AbstractUnitTestCase
{
    /**
     * @var TcaTableService
     */
    protected $subject;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @var DatabaseConnection
     */
    protected $databaseConnection;

    public function setUp()
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)->getMock();
        $this->databaseConnection = $this->getMockBuilder(DatabaseConnection::class)->getMock();

        $className = TcaTableService::class;
        if ($this->isLegacyVersion()) {
            $className = TcaTableService76::class;
        }
        $this->subject = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->setMethods(['getConnection', 'getSystemWhereClause'])
            ->getMock();
        $this->subject->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->databaseConnection);

        $this->inject($this->subject, 'configuration', $this->configuration);
        $this->inject($this->subject, 'logger', $this->getMockedLogger());
        $this->inject($this->subject, 'tableName', 'table');
    }

    /**
     * @test
     */
    public function doUsePlainQueryIfNoAdditionalWhereClauseIsDefined()
    {
        $this->markTestSkipped('We have to migrate this test for TYPO3 CMS 8.x');
        $this->configuration->expects($this->exactly(2))
            ->method('getIfExists')
            ->withConsecutive(['indexing.table.additionalWhereClause'], ['indexing.table.rootLineBlacklist'])
            ->will($this->onConsecutiveCalls(null, false));
        $this->subject->expects($this->once())
            ->method('getSystemWhereClause')
            ->will($this->returnValue('1=1 AND pages.no_search = 0'));

        $this->assertSame(
            '1=1 AND pages.no_search = 0',
            $whereClause->getStatement()
        );
        $this->assertSame(
            [],
            $whereClause->getParameters()
        );
    }

    /**
     * @test
     */
    public function configuredAdditionalWhereClauseIsAdded()
    {
        $this->markTestSkipped('We have to migrate this test for TYPO3 CMS 8.x');
        $this->configuration->expects($this->exactly(2))
            ->method('getIfExists')
            ->withConsecutive(['indexing.table.additionalWhereClause'], ['indexing.table.rootLineBlacklist'])
            ->will($this->onConsecutiveCalls('table.field = "someValue"', false));

        $this->subject->expects($this->once())
            ->method('getSystemWhereClause')
            ->will($this->returnValue('1=1 AND pages.no_search = 0'));

        $this->subject->getRecord(10);

        // $whereClause = $this->subject->getWhereClause();
        // $this->assertSame(
        //     '1=1 AND pages.no_search = 0 AND table.field = "someValue"',
        //     $whereClause->getStatement()
        // );
        // $this->assertSame(
        //     [],
        //     $whereClause->getParameters()
        // );
    }

    /**
     * @test
     */
    public function allConfiguredAndAllowedTcaColumnsAreReturnedAsFields()
    {
        $GLOBALS['TCA']['test_table'] = [
            'ctrl' => [
                'languageField' => 'sys_language',
            ],
            'columns' => [
                'sys_language' => [],
                't3ver_oid' => [],
                'available_column' => [
                    'config' => [
                        'type' => 'input',
                    ],
                ],
                'user_column' => [
                    'config' => [
                        'type' => 'user',
                    ],
                ],
                'passthrough_column' => [
                    'config' => [
                        'type' => 'passthrough',
                    ],
                ],
            ],
        ];
        $subject = new TcaTableService(
            'test_table',
            $this->getMockBuilder(RelationResolver::class)->getMock(),
            $this->configuration
        );
        $this->inject($subject, 'logger', $this->getMockedLogger());

        // $this->assertSame(
        //     [
        //         'test_table.uid',
        //         'test_table.pid',
        //         'test_table.available_column',
        //     ],
        //     $subject->getFields(),
        //     ''
        // );
        unset($GLOBALS['TCA']['test_table']);
    }
}
