<?php
namespace Codappix\SearchCore\Tests\Unit\Configuration;

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

use Codappix\SearchCore\Configuration\ConfigurationUtility;
use Codappix\SearchCore\Connection\SearchRequestInterface;
use Codappix\SearchCore\Domain\Model\SearchRequest;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class ConfigurationUtilityTest extends AbstractUnitTestCase
{
    /**
     * @test
     * @dataProvider possibleRequestAndConfigurationForFluidtemplate
     */
    public function recursiveEntriesAreProcessedAsFluidtemplate(
        SearchRequestInterface $searchRequest,
        array $array,
        array $expected
    ) {
        $subject = new ConfigurationUtility();

        $this->assertSame(
            $expected,
            $subject->replaceArrayValuesWithRequestContent($searchRequest, $array),
            'Entries in array were not parsed as fluid template with search request.'
        );
    }

    public function possibleRequestAndConfigurationForFluidtemplate() : array
    {
        return [
            'Nothing in array' => [
                'searchRequest' => new SearchRequest(),
                'array' => [],
                'expected' => [],
            ],
            'Small array with nothing to replace' => [
                'searchRequest' => new SearchRequest(),
                'array' => [
                    'key1' => 'value1',
                ],
                'expected' => [
                    'key1' => 'value1',
                ],
            ],
            'Rescursive array with replacements' => [
                'searchRequest' => call_user_func(function () {
                    $request = new SearchRequest();
                    $request->setFilter([
                        'distance' => [
                            'location' => '10',
                        ],
                    ]);
                    return $request;
                }),
                'array' => [
                    'sub1' => [
                        'sub1.1' => '{request.filter.distance.location}',
                        'sub1.2' => '{request.nonExisting}',
                    ],
                ],
                'expected' => [
                    'sub1' => [
                        // Numberics are casted to double
                        'sub1.1' => 10.0,
                        'sub1.2' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider possibleConditionEntries
     */
    public function conditionsAreHandledAsExpected(array $entries, array $expected)
    {
        $subject = new ConfigurationUtility();

        $this->assertSame(
            $expected,
            $subject->filterByCondition($entries),
            'Conditions were not processed as expected.'
        );
    }

    public function possibleConditionEntries() : array
    {
        return [
            'Nothing in array' => [
                'entries' => [],
                'expected' => [],
            ],
            'Entries without condition' => [
                'entries' => [
                    'key1' => 'value1',
                ],
                'expected' => [
                    'key1' => 'value1',
                ],
            ],
            'Entry with matching condition' => [
                'entries' => [
                    'sub1' => [
                        'condition' => true,
                        'sub1.2' => 'something',
                    ],
                ],
                'expected' => [
                    'sub1' => [
                        'sub1.2' => 'something',
                    ],
                ],
            ],
            'Entry with non matching condition' => [
                'entries' => [
                    'sub1' => [
                        'condition' => false,
                        'sub1.2' => 'something',
                    ],
                ],
                'expected' => [],
            ],
        ];
    }
}
