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

class CopyToProcessorTest extends AbstractUnitTestCase
{
    /**
     * @param array $record
     * @param array $configuration
     * @param array $expectedRecord
     *
     * @test
     * @dataProvider getTestData
     */
    public function recordIsReturnedExpectedtoProvidedConfiguration(array $record, array $configuration, array $expectedRecord)
    {
        $processor = new CopyToProcessor();

        $this->assertSame(
            $expectedRecord,
            $processor->processRecord($record, $configuration),
            ''
        );
    }

    public function getTestData()
    {
        return [
            'copy all' => [
                'record' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                ],
                'configuration' => [
                    'to' => 'new field',
                ],
                'expectedRecord' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                    'new field' => "Some content\nAnother content",
                ],
            ],
            'copy one' => [
                'record' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                ],
                'configuration' => [
                    'from' => 'field_1',
                    'to' => 'new field',
                ],
                'expectedRecord' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                    'new field' => 'Some content',
                ],
            ],
            'copy some' => [
                'record' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                    'field_3' => 'Another one',
                ],
                'configuration' => [
                    'from' => 'field_1, field_3',
                    'to' => 'new field',
                ],
                'expectedRecord' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                    'field_3' => 'Another one',
                    'new field' => "Some content\nAnother one",
                ],
            ],
            'configure to copy non existing fields' => [
                'record' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                ],
                'configuration' => [
                    'from' => 'non_existing, field_1',
                    'to' => 'new field',
                ],
                'expectedRecord' => [
                    'field_1' => 'Some content',
                    'field_2' => 'Another content',
                    'new field' => 'Some content',
                ],
            ],
        ];
    }
}
