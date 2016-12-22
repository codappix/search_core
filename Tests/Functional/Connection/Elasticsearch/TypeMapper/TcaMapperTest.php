<?php
namespace Leonmrni\SearchCore\Tests\Functional\Connection\Elasticsearch\TypeMapper;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
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

use Leonmrni\SearchCore\Connection\Elasticsearch\TypeMapper\TcaMapper;
use Leonmrni\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TcaMapperTest extends AbstractFunctionalTestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        parent::setUp();

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    // Mapping generation

    /**
     * @test
     */
    public function generatesMappingForDates()
    {
        $this->loadPhpFixture('Tests/Functional/Fixtures/Connection/Elasticsearch/TypeMapper/Tca/DateFields.php');
        $subject = $this->objectManager->get(TcaMapper::class, 'test_table');

        $this->assertSame(
            [
                'createDate' => [
                    'type' => 'date',
                    'format' => 'date_optional_time',
                ],
                'date' => [
                    'type' => 'date',
                    'format' => 'date_optional_time',
                ],
                'time' => [
                    'type' => 'date',
                    'format' => 'date_optional_time',
                ],
                'datetime' => [
                    'type' => 'date',
                    'format' => 'date_optional_time',
                ],
            ],
            $subject->getPropertyMapping(),
            'Tca Mapper did not respect date fields.'
        );
    }

    /**
     * @test
     */
    public function generatesMappingForBoolean()
    {
        $this->loadPhpFixture('Tests/Functional/Fixtures/Connection/Elasticsearch/TypeMapper/Tca/BooleanFields.php');
        $subject = $this->objectManager->get(TcaMapper::class, 'test_table');

        $this->assertSame(
            [
                'deleted' => [
                    'type' => 'boolean',
                ],
                'hidden' => [
                    'type' => 'boolean',
                ],
            ],
            $subject->getPropertyMapping(),
            'Tca Mapper did not respect boolean fields.'
        );
    }

    /**
     * @test
     */
    public function generatesMappingForKeywords()
    {
        $this->loadPhpFixture('Tests/Functional/Fixtures/Connection/Elasticsearch/TypeMapper/Tca/KeywordFields.php');
        $subject = $this->objectManager->get(TcaMapper::class, 'test_table');

        $this->assertSame(
            [
                'CType' => [
                    'type' => 'keyword',
                ],
            ],
            $subject->getPropertyMapping(),
            'Tca Mapper did not respect keyword fields.'
        );
    }

    // Mapping applying

    /**
     * @test
     */
    public function appliesMappingForDates()
    {
        $this->loadPhpFixture('Tests/Functional/Fixtures/Connection/Elasticsearch/TypeMapper/Tca/DateFields.php');
        $timestamp = 1482419147;
        date_default_timezone_set('Europe/London');
        $formattedDate = date('c', $timestamp);
        $record = [
            'createDate' => $timestamp,
            'date' => $timestamp,
            'time' => $timestamp,
            'datetime' => $timestamp,
            'someUnconfiguredField' => $timestamp,
        ];
        $subject = $this->objectManager->get(TcaMapper::class, 'test_table');
        $subject->applyMappingToDocument($record);

        $this->assertSame(
            [
                'createDate' => $formattedDate,
                'date' => $formattedDate,
                'time' => $formattedDate,
                'datetime' => $formattedDate,
                'someUnconfiguredField' => $timestamp,
            ],
            $record,
            'Tca Mapper did not respect date fields.'
        );
    }

    /**
     * @test
     */
    public function appliesMappingForBoolean()
    {
        $this->loadPhpFixture('Tests/Functional/Fixtures/Connection/Elasticsearch/TypeMapper/Tca/BooleanFields.php');
        $record = [
            'deleted' => '1',
            'hidden' => '0',
            'someUnconfiguredField' => $timestamp,
        ];
        $subject = $this->objectManager->get(TcaMapper::class, 'test_table');
        $subject->applyMappingToDocument($record);

        $this->assertSame(
            [
                'deleted' => true,
                'hidden' => false,
                'someUnconfiguredField' => $timestamp,
            ],
            $record,
            'Tca Mapper did not respect boolean fields.'
        );
    }

    /**
     * @test
     */
    public function appliesMappingForKeywords()
    {
        $this->loadPhpFixture('Tests/Functional/Fixtures/Connection/Elasticsearch/TypeMapper/Tca/KeywordFields.php');
        $record = [
            'CType' => 'list',
            'someUnconfiguredField' => $timestamp,
        ];
        $subject = $this->objectManager->get(TcaMapper::class, 'test_table');
        $subject->applyMappingToDocument($record);

        $this->assertSame(
            [
                'CType' => 'list',
                'someUnconfiguredField' => $timestamp,
            ],
            $record,
            'Tca Mapper did not respect boolean fields.'
        );
    }
}
