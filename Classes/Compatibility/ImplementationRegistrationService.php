<?php
namespace Codappix\SearchCore\Compatibility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Object\Container\Container;

/**
 * Register different concrete implementations, depending on current TYPO3 version.
 * This way we can provide working implementations for multiple TYPO3 versions.
 */
class ImplementationRegistrationService
{
    public static function registerImplementations()
    {
        $container = GeneralUtility::makeInstance(Container::class);
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 8000000) {
            $container->registerImplementation(
                \Codappix\SearchCore\Compatibility\TypoScriptServiceInterface::class,
                \Codappix\SearchCore\Compatibility\TypoScriptService::class
            );
            $container->registerImplementation(
                \Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableServiceInterface::class,
                \Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableService::class
            );
        } else {
            $container->registerImplementation(
                \Codappix\SearchCore\Compatibility\TypoScriptServiceInterface::class,
                \Codappix\SearchCore\Compatibility\TypoScriptService76::class
            );
            $container->registerImplementation(
                \Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableServiceInterface::class,
                \Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableService76::class
            );
        }
    }
}
