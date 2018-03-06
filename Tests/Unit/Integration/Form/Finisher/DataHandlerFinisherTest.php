<?php
namespace Codappix\SearchCore\Tests\Unit\Integration\Form\Finisher;

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

use Codappix\SearchCore\Domain\Service\DataHandler;
use Codappix\SearchCore\Integration\Form\Finisher\DataHandlerFinisher;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Finishers\FinisherContext;

class DataHandlerFinisherTest extends AbstractUnitTestCase
{
    /**
     * @var DataHandlerFinisher
     */
    protected $subject;

    /**
     * @var DataHandler
     */
    protected $dataHandlerMock;

    /**
     * @var FinisherContext
     */
    protected $finisherContextMock;

    public function setUp()
    {
        parent::setUp();

        $this->configureMockedTranslationService();
        $this->dataHandlerMock = $this->getMockBuilder(DataHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->finisherContextMock = $this->getMockBuilder(FinisherContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = new DataHandlerFinisher();
        $this->inject($this->subject, 'dataHandler', $this->dataHandlerMock);
    }

    /**
     * @test
     * @dataProvider possibleFinisherSetup
     */
    public function validConfiguration(string $action, array $nonCalledActions, $expectedSecondArgument)
    {
        $this->subject->setOptions([
            'indexIdentifier' => 'test_identifier',
            'recordUid' => '23',
            'action' => $action,
        ]);

        foreach ($nonCalledActions as $nonCalledAction) {
            $this->dataHandlerMock->expects($this->never())->method($nonCalledAction);
        }
        $this->dataHandlerMock->expects($this->once())->method($action)
            ->with('test_identifier', $expectedSecondArgument);

        $this->subject->execute($this->finisherContextMock);
    }

    public function possibleFinisherSetup() : array
    {
        return [
            'valid update configuration' => [
                'action' => 'update',
                'nonCalledActions' => ['delete'],
                'expectedSecondArgument' => ['uid' => 23],
            ],
            'valid delete configuration' => [
                'action' => 'delete',
                'nonCalledActions' => ['update'],
                'expectedSecondArgument' => 23,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidFinisherSetup
     */
    public function nothingHappensIfUnknownActionIsConfigured(array $options)
    {
        $this->subject->setOptions($options);

        foreach (['update', 'delete'] as $nonCalledAction) {
            $this->dataHandlerMock->expects($this->never())->method($nonCalledAction);
        }

        $this->expectException(FinisherException::class);
        $this->subject->execute($this->finisherContextMock);
    }

    public function invalidFinisherSetup() : array
    {
        return [
            'missing options' => [
                'options' => [],
            ],
            'missing action option' => [
                'options' => [
                    'indexIdentifier' => 'identifier',
                    'recordUid' => '20',
                ],
            ],
            'missing record uid option' => [
                'options' => [
                    'indexIdentifier' => 'identifier',
                    'action' => 'update',
                ],
            ],
        ];
    }
}
