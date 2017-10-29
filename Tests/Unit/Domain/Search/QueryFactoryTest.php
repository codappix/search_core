<?php
namespace Codappix\SearchCore\Tests\Unit\Domain\Search;

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
use Codappix\SearchCore\Configuration\ConfigurationUtility;
use Codappix\SearchCore\Configuration\InvalidArgumentException;
use Codappix\SearchCore\Domain\Model\FacetRequest;
use Codappix\SearchCore\Domain\Model\SearchRequest;
use Codappix\SearchCore\Domain\Search\QueryFactory;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class QueryFactoryTest extends AbstractUnitTestCase
{
    /**
     * @var QueryFactory
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
        $configurationUtility = new ConfigurationUtility();
        $this->subject = new QueryFactory($this->getMockedLogger(), $this->configuration, $configurationUtility);
    }

    /**
     * @test
     */
    public function creationOfQueryWorksInGeneral()
    {
        $searchRequest = new SearchRequest('SearchWord');

        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));

        $query = $this->subject->create($searchRequest);
        $this->assertInstanceOf(
            \Elastica\Query::class,
            $query,
            'Factory did not create the expected instance.'
        );
    }

    /**
     * @test
     */
    public function filterIsAddedToQuery()
    {
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));

        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->setFilter(['field' => 'content']);

        $query = $this->subject->create($searchRequest);
        $this->assertSame(
            [
                ['term' => ['field' => 'content']]
            ],
            $query->toArray()['query']['bool']['filter'],
            'Filter was not added to query.'
        );
    }

    /**
     * @test
     */
    public function emptyFilterIsNotAddedToQuery()
    {
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));

        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->setFilter([
            'field' => '',
        ]);

        $this->assertFalse(
            $searchRequest->hasFilter(),
            'Search request contains filter even if it should not.'
        );

        $query = $this->subject->create($searchRequest);
        $this->assertSame(
            null,
            $query->toArray()['query']['bool']['filter'],
            'Filter was added to query, even if no filter exists.'
        );
    }

    /**
     * @test
     */
    public function facetsAreAddedToQuery()
    {
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));
        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->addFacet(new FacetRequest('Identifier', 'FieldName'));
        $searchRequest->addFacet(new FacetRequest('Identifier 2', 'FieldName 2'));

        $query = $this->subject->create($searchRequest);
        $this->assertSame(
            [
                'Identifier' => [
                    'terms' => [
                        'field' => 'FieldName',
                    ],
                ],
                'Identifier 2' => [
                    'terms' => [
                        'field' => 'FieldName 2',
                    ],
                ],
            ],
            $query->toArray()['aggs'],
            'Facets were not added to query.'
        );
    }

    /**
     * @test
     */
    public function sizeIsAddedToQuery()
    {
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));
        $searchRequest = new SearchRequest('SearchWord');
        $searchRequest->setLimit(45);
        $searchRequest->setOffset(35);

        $query = $this->subject->create($searchRequest);
        $this->assertSame(
            45,
            $query->toArray()['size'],
            'Limit was not added to query.'
        );
        $this->assertSame(
            35,
            $query->toArray()['from'],
            'From was not added to query.'
        );
    }

    /**
     * @test
     */
    public function searchTermIsAddedToQuery()
    {
        $searchRequest = new SearchRequest('SearchWord');
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));
        $query = $this->subject->create($searchRequest);

        $this->assertSame(
            [
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                '_all' => [
                                    'query' => 'SearchWord',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $query->toArray()['query'],
            'Search term was not added to query as expected.'
        );
    }

    /**
     * @test
     */
    public function minimumShouldMatchIsAddedToQuery()
    {
        $searchRequest = new SearchRequest('SearchWord');
        $this->configuration->expects($this->any())
            ->method('getIfExists')
            ->withConsecutive(
                ['searching.minimumShouldMatch'],
                ['searching.sort']
            )
            ->will($this->onConsecutiveCalls(
                '50%',
                null
            ));
        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));
        $query = $this->subject->create($searchRequest);

        $this->assertArraySubset(
            [
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                '_all' => [
                                    'minimum_should_match' => '50%',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $query->toArray()['query'],
            'minimum_should_match was not added to query as configured.'
        );
    }

    /**
     * @test
     */
    public function boostsAreAddedToQuery()
    {
        $searchRequest = new SearchRequest('SearchWord');

        $this->configuration->expects($this->any())
            ->method('get')
            ->withConsecutive(
                ['searching.boost'],
                ['searching.fields.stored_fields'],
                ['searching.fields.script_fields'],
                ['searching.fieldValueFactor']
            )
            ->will($this->onConsecutiveCalls(
                [
                    'search_title' => 3,
                    'search_abstract' => 1.5,
                ],
                $this->throwException(new InvalidArgumentException),
                $this->throwException(new InvalidArgumentException),
                $this->throwException(new InvalidArgumentException)
            ));

        $query = $this->subject->create($searchRequest);
        $this->assertSame(
            [
                [
                    'match' => [
                        'search_title' => [
                            'query' => 'SearchWord',
                            'boost' => 3,
                        ],
                    ],
                ],
                [
                    'match' => [
                        'search_abstract' => [
                            'query' => 'SearchWord',
                            'boost' => 1.5,
                        ],
                    ],
                ],
            ],
            $query->toArray()['query']['bool']['should'],
            'Boosts were not added to query.'
        );
    }

    /**
     * @test
     */
    public function factorBoostIsAddedToQuery()
    {
        $searchRequest = new SearchRequest('SearchWord');
        $fieldConfig = [
            'field' => 'rootlineLevel',
            'modifier' => 'reciprocal',
            'factor' => '2',
            'missing' => '1',
        ];
        $this->configuration->expects($this->any())
            ->method('get')
            ->withConsecutive(
                ['searching.boost'],
                ['searching.fields.stored_fields'],
                ['searching.fields.script_fields'],
                ['searching.fieldValueFactor']
            )
            ->will($this->onConsecutiveCalls(
                $this->throwException(new InvalidArgumentException),
                $this->throwException(new InvalidArgumentException),
                $this->throwException(new InvalidArgumentException),
                $fieldConfig
            ));

        $query = $this->subject->create($searchRequest);
        $this->assertSame(
            [
                'function_score' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'match' => [
                                        '_all' => [
                                            'query' => 'SearchWord',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'field_value_factor' => $fieldConfig,
                ],
            ],
            $query->toArray()['query'],
            'Boosts were not added to query.'
        );
    }

    /**
     * @test
     */
    public function emptySearchStringWillNotAddSearchToQuery()
    {
        $searchRequest = new SearchRequest();

        $this->configuration->expects($this->any())
            ->method('get')
            ->will($this->throwException(new InvalidArgumentException));

        $query = $this->subject->create($searchRequest);
        $this->assertInstanceOf(
            stdClass,
            $query->toArray()['query']['match_all'],
            'Empty search request does not create expected query.'
        );
    }
}
