<?php
namespace Leonmrni\SearchCore\Tests\Functional\Hooks\DataHandler;

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

use Leonmrni\SearchCore\Configuration\ConfigurationContainerInterface;
use Leonmrni\SearchCore\Domain\Service\DataHandler as DataHandlerService;
use Leonmrni\SearchCore\Hook\DataHandler as DataHandlerHook;
use Leonmrni\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class IgnoresUnkownOperationTest extends AbstractFunctionalTestCase
{
    /**
     * @var DataHandlerService|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->subject = $this->getAccessibleMock(
            DataHandlerService::class,
            [
                'add',
                'update',
                'delete',
            ],
            [$objectManager->get(ConfigurationContainerInterface::class)]
        );

        // This way TYPO3 will use our mock instead of a new instance.
        $GLOBALS['T3_VAR']['getUserObj']['&' . DataHandlerHook::class] = new DataHandlerHook($this->subject);
    }

    /**
     * @test
     */
    public function deletionWillBeTriggeredForTtContent()
    {
        $this->subject->expects($this->exactly(0))->method('add');
        $this->subject->expects($this->exactly(0))->method('update');
        $this->subject->expects($this->exactly(0))->method('delete');

        $tce = GeneralUtility::makeInstance(Typo3DataHandler::class);
        $tce->stripslashes_values = 0;
        $tce->start([], [
            'tt_content' => [
                '1' => [
                    'something' => 1,
                ],
            ],
        ]);
        $tce->process_cmdmap();
    }
}
