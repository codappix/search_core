<?php
namespace Codappix\SearchCore\Tests\Unit\Domain\Index;

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
use Codappix\SearchCore\DataProcessing\CopyToProcessor;
use Codappix\SearchCore\Domain\Index\AbstractIndexer;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class AbstractIndexerTest extends AbstractUnitTestCase
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
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function setUp()
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)->getMock();
        $this->connection = $this->getMockBuilder(ConnectionInterface::class)->getMock();
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();


        $this->subject = $this->getMockForAbstractClass(AbstractIndexer::class, [
            $this->connection,
            $this->configuration
        ]);
        $this->inject($this->subject, 'objectManager', $this->objectManager);
        $this->subject->injectLogger($this->getMockedLogger());
        $this->subject->setIdentifier('testTable');
        $this->subject->expects($this->any())
            ->method('getDocumentName')
            ->willReturn('testTable');
    }

    /**
     * @test
     */
    public function executesConfiguredDataProcessingWithConfiguration()
    {
        $record = ['field 1' => 'test'];
        $expectedRecord = $record;
        $expectedRecord['new_test_field'] = 'test';
        $expectedRecord['new_test_field2'] = 'test' . PHP_EOL . 'test';
        $expectedRecord['search_abstract'] = '';

        $this->objectManager->expects($this->any())
            ->method('get')
            ->with(CopyToProcessor::class)
            ->willReturn(new CopyToProcessor());

        $this->configuration->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['indexing.testTable.dataProcessing'], ['indexing.testTable.abstractFields'])
            ->will($this->onConsecutiveCalls([
                '1' => [
                    '_typoScriptNodeValue' => CopyToProcessor::class,
                    'to' => 'new_test_field',
                ],
                '2' => [
                    '_typoScriptNodeValue' => CopyToProcessor::class,
                    'to' => 'new_test_field2',
                ],
            ], $this->throwException(new InvalidArgumentException)));
        $this->subject->expects($this->once())
            ->method('getRecord')
            ->with(1)
            ->willReturn($record)
            ;

        $this->connection->expects($this->once())->method('addDocument')->with('testTable', $expectedRecord);
        $this->subject->indexDocument(1);
    }

    /**
     * @test
     */
    public function executesNoDataProcessingForMissingConfiguration()
    {
        $record = ['field 1' => 'test'];
        $expectedRecord = $record;
        $expectedRecord['search_abstract'] = '';

        $this->configuration->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['indexing.testTable.dataProcessing'], ['indexing.testTable.abstractFields'])
            ->will($this->throwException(new InvalidArgumentException));
        $this->subject->expects($this->once())
            ->method('getRecord')
            ->with(1)
            ->willReturn($record)
            ;

        $this->connection->expects($this->once())->method('addDocument')->with('testTable', $expectedRecord);
        $this->subject->indexDocument(1);
    }
}
