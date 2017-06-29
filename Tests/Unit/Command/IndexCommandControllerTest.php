<?php
namespace Leonmrni\SearchCore\Tests\Unit\Command;

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

use Leonmrni\SearchCore\Command\IndexCommandController;
use Leonmrni\SearchCore\Configuration\ConfigurationContainerInterface;
use Leonmrni\SearchCore\Domain\Index\IndexerFactory;
use Leonmrni\SearchCore\Domain\Index\TcaIndexer;
use Leonmrni\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;

class IndexCommandControllerTest extends AbstractUnitTestCase
{
    /**
     * @var IndexCommandController
     */
    protected $subject;

    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    public function setUp()
    {
        parent::setUp();

        $this->indexerFactory = $this->getMockBuilder(IndexerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = $this->getMockBuilder(IndexCommandController::class)
            ->disableOriginalConstructor()
            ->setMethods(['quit', 'outputLine'])
            ->getMock();
        $this->subject->injectIndexerFactory($this->indexerFactory);
        $this->inject($this->subject, 'configuration', $this->configuration);
    }

    /**
     * @test
     */
    public function indexerStopsForNonAllowedTable()
    {
        $this->expectException(StopActionException::class);
        $this->subject->expects($this->once())
            ->method('quit')
            ->with(1)
            ->will($this->throwException(new StopActionException));

        $this->subject->expects($this->once())
            ->method('outputLine')
            ->with('Table is not allowed for indexing.');
        $this->indexerFactory->expects($this->never())
            ->method('getIndexer');

        $this->configuration->expects($this->once())
            ->method('getIfExists')
            ->with('indexing.nonAllowedTable')
            ->will($this->returnValue(null));
        $this->subject->indexCommand('nonAllowedTable');
    }

    /**
     * @test
     */
    public function indexerExecutesForAllowedTable()
    {
        $indexerMock = $this->getMockBuilder(TcaIndexer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->expects($this->never())
            ->method('quit');
        $this->subject->expects($this->once())
            ->method('outputLine')
            ->with('Table was indexed.');
        $this->indexerFactory->expects($this->once())
            ->method('getIndexer')
            ->with('allowedTable')
            ->will($this->returnValue($indexerMock));

        $this->configuration->expects($this->once())
            ->method('getIfExists')
            ->with('indexing.allowedTable')
            ->will($this->returnValue([
                'indexer' => 'Leonmrni\SearchCore\Domain\Index\TcaIndexer',
            ]));
        $this->subject->indexCommand('allowedTable');
    }
}
