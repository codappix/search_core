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
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class MappingFactoryTest extends AbstractUnitTestCase
{
    /**
     * @var MappingFactory
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->configuration = $this->getMockBuilder(ConfigurationContainerInterface::class)->getMock();
        $this->subject = new MappingFactory($this->configuration);
    }

    /**
     * @test
     */
    public function typoScriptConfigurationIsProvidedToIndex()
    {
        $indexName = 'someIndex';
        $configuration = [
            '_all' => [
                'type' => 'text',
                'analyzer' => 'ngram4',
            ],
            'channel' => [
                'type' => 'keyword',
            ],
        ];
        $type = $this->getMockBuilder(\Elastica\Type::class)
            ->disableOriginalConstructor()
            ->getMock();
        $type->expects($this->any())
            ->method('getName')
            ->willReturn($indexName);
        $this->configuration->expects($this->once())
            ->method('get')
            ->with('indexing.' . $indexName . '.mapping')
            ->willReturn($configuration);

        $mapping = $this->subject->getMapping($type)->toArray()[$indexName];
        $this->assertArraySubset(
            [
                '_all' => $configuration['_all']
            ],
            $mapping,
            true,
            'Configuration of _all field was not set for mapping.'
        );
        $this->assertArraySubset(
            [
                'channel' => $configuration['channel']
            ],
            $mapping['properties'],
            true,
            'Configuration for properties was not set for mapping.'
        );
    }
}
