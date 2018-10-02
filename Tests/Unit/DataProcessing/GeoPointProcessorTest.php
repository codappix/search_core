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

use Codappix\SearchCore\DataProcessing\GeoPointProcessor;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class GeoPointProcessorTest extends AbstractUnitTestCase
{
    /**
     * @test
     * @dataProvider getPossibleDataConfigurationCombinations
     * @param array $record
     * @param array $configuration
     * @param array $expectedData
     */
    public function geoPointsAreAddedAsConfigured(array $record, array $configuration, array $expectedData)
    {
        $subject = new GeoPointProcessor();
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
            'Create new field with existing lat and lng' => [
                'record' => [
                    'lat' => 23.232,
                    'lng' => 45.43,
                ],
                'configuration' => [
                    'to' => 'location',
                    'lat' => 'lat',
                    'lon' => 'lng',
                ],
                'expectedData' => [
                    'lat' => 23.232,
                    'lng' => 45.43,
                    'location' => [
                        'lat' => 23.232,
                        'lon' => 45.43,
                    ],
                ],
            ],
            'Do not create new field due to missing configuration' => [
                'record' => [
                    'lat' => 23.232,
                    'lng' => 45.43,
                ],
                'configuration' => [
                    'to' => 'location',
                ],
                'expectedData' => [
                    'lat' => 23.232,
                    'lng' => 45.43,
                ],
            ],
            'Do not create new field due to missing lat and lon' => [
                'record' => [
                    'lat' => '',
                    'lng' => '',
                ],
                'configuration' => [
                    'to' => 'location',
                    'lat' => 'lat',
                    'lon' => 'lng',
                ],
                'expectedData' => [
                    'lat' => '',
                    'lng' => '',
                ],
            ],
            'Do not create new field due to invalid lat and lon' => [
                'record' => [
                    'lat' => 'av',
                    'lng' => 'dsf',
                ],
                'configuration' => [
                    'to' => 'location',
                    'lat' => 'lat',
                    'lon' => 'lng',
                ],
                'expectedData' => [
                    'lat' => 'av',
                    'lng' => 'dsf',
                ],
            ],
        ];
    }
}
