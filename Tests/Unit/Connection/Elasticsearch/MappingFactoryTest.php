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
use Codappix\SearchCore\Connection\Elasticsearch\MappingFactory;
use Codappix\SearchCore\Connection\Elasticsearch\TypeFactory;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use Elastica\Type;

class MappingFactoryTest extends AbstractUnitTestCase
{
    /**
     * @var MappingFactory
     */
    protected $subject;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configurationMock;

    /**
     * @var TypeFactory
     */
    protected $typeFactoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->configurationMock = $this->getMockBuilder(ConfigurationContainerInterface::class)->getMock();
        $this->typeFactoryMock = $this->getMockBuilder(TypeFactory::class)->disableOriginalConstructor()->getMock();

        $this->subject = new MappingFactory($this->configurationMock, $this->typeFactoryMock);
    }

    /**
     * @test
     */
    public function typoScriptConfigurationIsProvidedToIndex()
    {
        $documentType = 'someDocument';
        $configuration = [
            'channel' => [
                'type' => 'keyword',
            ],
        ];

        $typeMock = $this->getMockBuilder(Type::class)->disableOriginalConstructor()->getMock();

        $this->typeFactoryMock->expects($this->any())
            ->method('getType')
            ->with($documentType)
            ->willReturn($typeMock);
        $this->configurationMock->expects($this->once())
            ->method('get')
            ->with('indexing.' . $documentType . '.mapping')
            ->willReturn($configuration);

        $mapping = $this->subject->getMapping($documentType)->toArray()[''];
        $this->assertArraySubset(
            $configuration,
            $mapping['properties'],
            true,
            'Configuration for properties was not set for mapping.'
        );
    }
}
