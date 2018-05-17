<?php
namespace Codappix\SearchCore\Tests\Unit\Connection\Elasticsearch;

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
use Codappix\SearchCore\Connection\Elasticsearch\Connection;
use Codappix\SearchCore\Connection\Elasticsearch\IndexFactory;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use PHPUnit_Framework_MockObject_MockObject;

class IndexFactoryTest extends AbstractUnitTestCase
{
    /**
     * @var IndexFactory
     */
    protected $subject;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    public function setUp()
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)->getMock();
        $this->subject = new IndexFactory($this->configuration);
        $this->subject->injectLogger($this->getMockedLogger());
    }

    /**
     * @test
     */
    public function indexIsNotCreatedIfAlreadyExisting()
    {
        $indexMock = $this->getMockBuilder(\Elastica\Index::class)
            ->disableOriginalConstructor()
            ->getMock();
        $indexMock->expects($this->once())
            ->method('exists')
            ->willReturn(true);
        $indexMock->expects($this->never())
            ->method('create');
        $clientMock = $this->getMockBuilder(\Elastica\Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientMock->expects($this->once())
            ->method('getIndex')
            ->with('typo3content')
            ->willReturn($indexMock);
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('getClient')
            ->willReturn($clientMock);

        $this->configuration->expects($this->once())
            ->method('get')
            ->with('connections.elasticsearch.index')
            ->willReturn('typo3content');

        $this->subject->getIndex($connection, 'someIndex');
    }

    /**
     * @test
     */
    public function typoScriptConfigurationIsProvidedToIndex()
    {
        $configuration = [
            'analysis' => [
                'analyzer' => [
                    'ngram4' => [
                        'type' => 'custom',
                        'tokenizer' => 'ngram4',
                        'char_filter' => 'html_strip',
                        'filter' => 'lowercase, ,  asciifolding',
                    ],
                ],
                'tokenizer' => [
                    'ngram4' => [
                        'type' => 'ngram',
                        'min_gram' => 4,
                        'max_gram' => 4,
                    ],
                ],
            ],
        ];

        $expectedConfiguration = $configuration;
        $expectedConfiguration['analysis']['analyzer']['ngram4']['char_filter'] = ['html_strip'];
        $expectedConfiguration['analysis']['analyzer']['ngram4']['filter'] = ['lowercase', 'asciifolding'];

        $indexMock = $this->getMockBuilder(\Elastica\Index::class)
            ->disableOriginalConstructor()
            ->getMock();
        $indexMock->expects($this->once())
            ->method('exists')
            ->willReturn(false);
        $indexMock->expects($this->once())
            ->method('create')
            ->with($expectedConfiguration);
        $clientMock = $this->getMockBuilder(\Elastica\Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientMock->expects($this->once())
            ->method('getIndex')
            ->with('typo3content')
            ->willReturn($indexMock);
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('getClient')
            ->willReturn($clientMock);

        $this->configuration->expects($this->exactly(2))
            ->method('get')
            ->will(
                $this->returnValueMap([
                    [
                        'indexing.someIndex.index',
                        $configuration
                    ],
                    [
                        'connections.elasticsearch.index',
                        'typo3content'
                    ]
                ])
            );

        $this->subject->getIndex($connection, 'someIndex');
    }
}
