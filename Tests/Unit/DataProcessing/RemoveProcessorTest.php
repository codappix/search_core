<?php

namespace Codappix\SearchCore\Tests\Unit\DataProcessing;

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

use Codappix\SearchCore\DataProcessing\RemoveProcessor;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class RemoveProcessorTest extends AbstractUnitTestCase
{
    /**
     * @test
     * @dataProvider getPossibleDataConfigurationCombinations
     * @param array $record
     * @param array $configuration
     * @param array $expectedData
     */
    public function fieldsAreCopiedAsConfigured(array $record, array $configuration, array $expectedData)
    {
        $subject = new RemoveProcessor();
        $processedData = $subject->processData($record, $configuration);
        $this->assertSame(
            $expectedData,
            $processedData,
            'The processor did not return the expected processed record.'
        );
    }

    /**
     * @return array
     */
    public function getPossibleDataConfigurationCombinations()
    {
        return [
            'Nothing configured' => [
                'record' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                ],
                'configuration' => [
                ],
                'expectedData' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                ],
            ],
            'Single field configured' => [
                'record' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                ],
                'configuration' => [
                    'fields' => 'field with sub2',
                    '_typoScriptNodeValue' => 'Codappix\SearchCore\DataProcessing\RemoveProcessor',
                ],
                'expectedData' => [
                    'field 1' => 'Some content like lorem',
                ],
            ],
            'Non existing field configured' => [
                'record' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                ],
                'configuration' => [
                    'fields' => 'non existing',
                    '_typoScriptNodeValue' => 'Codappix\SearchCore\DataProcessing\RemoveProcessor',
                ],
                'expectedData' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                ],
            ],
            'Multiple fields configured' => [
                'record' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                    'field 3' => 'Some more like lorem',
                ],
                'configuration' => [
                    'fields' => 'field 3, field with sub2',
                    '_typoScriptNodeValue' => 'Codappix\SearchCore\DataProcessing\RemoveProcessor',
                ],
                'expectedData' => [
                    'field 1' => 'Some content like lorem',
                ],
            ],
            'Fields with "null" san be removed' => [
                'record' => [
                    'field 1' => null,
                ],
                'configuration' => [
                    'fields' => 'field 1',
                    '_typoScriptNodeValue' => 'Codappix\SearchCore\DataProcessing\RemoveProcessor',
                ],
                'expectedData' => [
                ],
            ],
        ];
    }
}
