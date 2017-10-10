<?php
namespace Codappix\SearchCore\Tests\Functional\Hooks\DataHandler;

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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Domain\Index\IndexerFactory;
use Codappix\SearchCore\Domain\Service\DataHandler as DataHandlerService;
use Codappix\SearchCore\Hook\DataHandler as DataHandlerHook;
use Codappix\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

abstract class AbstractDataHandlerTest extends AbstractFunctionalTestCase
{
    /**
     * @var DataHandlerService|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->subject = $this->getMockBuilder(DataHandlerService::class)
            ->setConstructorArgs([
                $objectManager->get(ConfigurationContainerInterface::class),
                $objectManager->get(IndexerFactory::class)
            ])
            ->setMethods(['add', 'update', 'delete'])
            ->getMock();

        GeneralUtility::setSingletonInstance(DataHandlerHook::class, new DataHandlerHook($this->subject));
    }
}
