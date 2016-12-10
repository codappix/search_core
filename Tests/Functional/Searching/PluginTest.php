<?php
namespace Leonmrni\SearchCore\Tests\Functional\Searching;

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

use Leonmrni\SearchCore\Domain\Index\IndexerFactory;
use Leonmrni\SearchCore\Tests\Functional\FunctionalTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 *
 */
class PluginTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->importDataSet('Tests/Functional/Fixtures/Indexing/IndexTcaTable.xml');
    }

    /**
     * @test
     */
    public function searchingDoesWork()
    {
        // First we need something to search for
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(IndexerFactory::class)
            ->getIndexer('tt_content')
            ->index()
            ;

        $response = $this->getFrontendResponse(1);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $response, '$response', 8, false );die;
    }
}
