<?php
namespace Codappix\SearchCore\Tests\Unit\DataProcessing;

/*
 * Copyright (C) 2018  Daniel Siepmann <coding@daniel-siepmann.de>
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
use Codappix\SearchCore\DataProcessing\TcaRelationResolvingProcessor;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaRelationResolvingProcessorTest extends AbstractUnitTestCase
{
    /**
     * @var TcaRelationResolvingProcessor
     */
    protected $subject;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configurationMock;

    public function setUp()
    {
        parent::setUp();
        $this->configurationMock = $this->getMockBuilder(ConfigurationContainerInterface::class)->getMock();

        GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                ConfigurationContainerInterface::class,
                get_class($this->configurationMock)
            );

        $this->subject = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(TcaRelationResolvingProcessor::class);

        $GLOBALS['LANG'] = $this->getMockBuilder(LanguageService::class)->getMock();
    }

    public function tearDown()
    {
        unset($GLOBALS['LANG']);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfTableIsNotConfigured()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->processData([], []);
    }

    /**
     * @test
     */
    public function sysLanguageUidZeroIsKept()
    {
        $originalRecord = [
            'sys_language_uid' => '0',
        ];
        $record = [
            'sys_language_uid' => 0,
        ];
        $GLOBALS['TCA'] = [
            'tt_content' => [
                'ctrl' => [
                    'languageField' => 'sys_language_uid',
                ],
                'columns' => [
                    'sys_language_uid' => [
                        'config' => [
                            'default' => 0,
                            'items' => [
                                [
                                    'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                                    '-1',
                                    'flags-multiple',
                                ],
                            ],
                            'renderType' => 'selectSingle',
                            'special' => 'languages',
                            'type' => 'select',
                            'exclude' => '1',
                            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
                        ],
                    ],
                ],
            ],
        ];
        $configuration = [
            '_table' => 'tt_content',
        ];
        $record = $this->subject->processData($originalRecord, $configuration);
        $this->assertSame(
            [
                'sys_language_uid' => 0,
            ],
            $record,
            'sys_language_uid was not kept as zero.'
        );
    }

    /**
     * @test
     */
    public function renderTypeInputDateTimeIsHandled()
    {
        $originalRecord = [
            'uid' => 10,
            'endtime' => 99999999999,
            'starttime' => 1523010960,
        ];
        $record = $originalRecord;
        $GLOBALS['TCA'] = [
            'tt_content' => [
                'columns' => [
                    'starttime' => [
                        'config' => [
                            'type' => 'input',
                            'default' => 0,
                            'eval' => 'datetime,int',
                            'renderType' => 'inputDateTime',
                        ],
                        'exclude' => true,
                        'l10n_display' => 'defaultAsReadonly',
                        'l10n_mode' => 'exclude',
                        'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                    ],
                    'endtime' => [
                        'config' => [
                            'type' => 'input',
                            'default' => 0,
                            'eval' => 'datetime,int',
                            'renderType' => 'inputDateTime',
                        ],
                        'exclude' => true,
                        'l10n_display' => 'defaultAsReadonly',
                        'l10n_mode' => 'exclude',
                        'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                    ],
                ],
            ],
        ];
        $configuration = [
            '_table' => 'tt_content',
            'excludeFields' => 'starttime',
        ];
        $record = $this->subject->processData($originalRecord, $configuration);
        $this->assertSame(
            [
                'uid' => '10',
                'endtime' => '16-11-38 09:46',
                'starttime' => 1523010960,
            ],
            $record,
            'Exclude fields were not respected.'
        );
    }
}
