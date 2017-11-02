<?php
namespace Codappix\SearchCore\Tests\Unit;

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

use TYPO3\CMS\Core\Tests\UnitTestCase as CoreTestCase;

abstract class AbstractUnitTestCase extends CoreTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Disable caching backends to make TYPO3 parts work in unit test mode.

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Cache\CacheManager::class
        )->setCacheConfigurations([
            'extbase_object' => [
                'backend' => \TYPO3\CMS\Core\Cache\Backend\NullBackend::class,
            ],
        ]);
    }

    /**
     * @return \TYPO3\CMS\Core\Log\LogManager
     */
    protected function getMockedLogger()
    {
        $logger = $this->getMockBuilder(\TYPO3\CMS\Core\Log\LogManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLogger'])
            ->getMock();
        $logger->expects($this->once())
            ->method('getLogger')
            ->will($this->returnValue(
                $this->getMockBuilder(\TYPO3\CMS\Core\Log\Logger::class)
                    ->disableOriginalConstructor()
                    ->getMock()
            ));

        return $logger;
    }
}
