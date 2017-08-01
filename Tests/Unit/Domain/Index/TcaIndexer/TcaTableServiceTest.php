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
use Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableService;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

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

    public function setUp()
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)->getMock();

        $this->subject = $this->getMockBuilder(TcaTableService::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getWhereClause', 'injectLogger', 'getTableName'])
            ->getMock();
        $this->inject($this->subject, 'configuration', $this->configuration);
        $this->inject($this->subject, 'logger', $this->getMockedLogger());
        $this->inject($this->subject, 'tableName', 'table');
    }

    /**
     * @test
     */
    public function doUsePlainQueryIfNoAdditionalWhereClauseIsDefined()
    {
        $this->configuration->expects($this->exactly(2))
            ->method('getIfExists')
            ->withConsecutive(['indexing.table.additionalWhereClause'], ['indexing.table.rootLineBlacklist'])
            ->will($this->onConsecutiveCalls(null, false));

        $this->assertSame(
            '1=1 AND pages.no_search = 0',
            $this->subject->getWhereClause()
        );
    }

    /**
     * @test
     */
    public function configuredAdditionalWhereClauseIsAdded()
    {
        $this->configuration->expects($this->exactly(2))
            ->method('getIfExists')
            ->withConsecutive(['indexing.table.additionalWhereClause'], ['indexing.table.rootLineBlacklist'])
            ->will($this->onConsecutiveCalls('table.field = "someValue"', false));

        $this->assertSame(
            '1=1 AND pages.no_search = 0 AND table.field = "someValue"',
            $this->subject->getWhereClause()
        );
    }

    /**
     * @test
     */
    public function executesConfiguredDataProcessing()
    {
        $this->configuration->expects($this->exactly(1))
            ->method('get')
            ->with('indexing.testTable.dataProcessing')
            ->will($this->returnValue([
                '1' => [
                    '_typoScriptNodeValue' => CopyToProcessor::class,
                    'to' => 'new_test_field',
                ],
                '2' => [
                    '_typoScriptNodeValue' => CopyToProcessor::class,
                    'to' => 'new_test_field2',
                ],
            ]));

        $subject = $this->getMockBuilder(TcaTableService::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['prepareRecord'])
            ->getMock();
        $this->inject($subject, 'configuration', $this->configuration);
        $this->inject($subject, 'tableName', 'testTable');
        $this->inject($subject, 'relationResolver', $this->getMockBuilder(RelationResolver::class)->getMock());

        $record = ['field 1' => 'test'];
        $expectedRecord = $record;
        $expectedRecord['new_test_field'] = 'test';
        $expectedRecord['new_test_field2'] = 'test' . PHP_EOL . 'test';

        $subject->prepareRecord($record);

        $this->assertSame(
            $expectedRecord,
            $record,
            'Dataprocessing is not executed by TcaTableService as expected.'
        );
    }
}
