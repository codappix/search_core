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

use Codappix\SearchCore\DataProcessing\CopyToProcessor;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class QueryFactoryTest extends AbstractUnitTestCase
{
    /**
     * @test
     * @dataProvider getPossibleRecordConfigurationCombinations
     */
    public function fieldsAreCopiedAsConfigured(array $record, array $configuration, array $expectedRecord)
    {
        $subject = new CopyToProcessor();
        $processedRecord = $subject->processRecord($record, $configuration);
        $this->assertSame(
            $expectedRecord,
            $processedRecord,
            'The processor did not return the expected processed record.'
        );
    }

    /**
     * @return array
     */
    public function getPossibleRecordConfigurationCombinations()
    {
        return [
            'Copy all fields to new field' => [
                'record' => [
                    'field 1' => 'Some content like lorem',
                    'field 2' => 'Some more content like ipsum',
                ],
                'configuration' => [
                    'to' => 'new_field',
                ],
                'expectedRecord' => [
                    'field 1' => 'Some content like lorem',
                    'field 2' => 'Some more content like ipsum',
                    'new_field' => 'Some content like lorem' . PHP_EOL . 'Some more content like ipsum',
                ],
            ],
            'Copy all fields with sub array to new field' => [
                'record' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                ],
                'configuration' => [
                    'to' => 'new_field',
                ],
                'expectedRecord' => [
                    'field 1' => 'Some content like lorem',
                    'field with sub2' => [
                        'Tag 1',
                        'Tag 2',
                    ],
                    'new_field' => 'Some content like lorem' . PHP_EOL . 'Tag 1' . PHP_EOL . 'Tag 2',
                ],
            ],
        ];
    }
}
