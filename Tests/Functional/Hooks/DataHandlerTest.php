<?php
namespace Leonmrni\SearchCore\Tests\Functional\Hooks;

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

use Leonmrni\SearchCore\Hook\DataHandler as Hook;
use Leonmrni\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Core\DataHandling\DataHandler as CoreDataHandler;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 *
 */
class DataHandlerTest extends AbstractFunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->importDataSet('Tests/Functional/Fixtures/Hooks/DataHandler.xml');
    }

    /**
     * @test
     */
    public function nonAllowedTablesWillNotBeProcessed()
    {
        $dataHandler = new CoreDataHandler();

        $hook = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Hook::class);
        $this->assertFalse($hook->processDatamap_afterDatabaseOperations('new', 'some_strange_table', 'NEW34', [], $dataHandler));
        $this->assertFalse($hook->processDatamap_afterDatabaseOperations('update', 'some_strange_table', 6, [], $dataHandler));
        $this->assertFalse($hook->processCmdmap_deleteAction('some_strange_table', 6, [], false, $dataHandler));
    }

    /**
     * @test
     */
    public function addNewElement()
    {
        $dataHandler = new CoreDataHandler();
        $dataHandler->substNEWwithIDs = ['NEW34' => 6];

        $hook = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Hook::class);
        $hook->processDatamap_afterDatabaseOperations('new', 'tt_content', 'NEW34', [], $dataHandler);

        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertTrue($response->isOK());
        $this->assertSame($response->getData()['hits']['total'], 1, 'Not exactly 1 document was indexed.');
    }

    /**
     * @test
     * TODO: Make sure the indexed document was updated, e.g. by changing some content.
     */
    public function updateExistingElement()
    {
        $dataHandler = new CoreDataHandler();
        $hook = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Hook::class);
        $hook->processDatamap_afterDatabaseOperations('update', 'tt_content', 6, [], $dataHandler);

        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertTrue($response->isOK(), 'Elastica did not answer with ok code.');
        $this->assertSame($response->getData()['hits']['total'], 1, 'Not exactly 1 document was indexed.');
    }

    /**
     * @test
     */
    public function deleteExistingElement()
    {
        $this->addNewElement();
        $dataHandler = new CoreDataHandler();
        $hook = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Hook::class);
        $hook->processCmdmap_deleteAction('tt_content', 6, [], false, $dataHandler);

        $response = $this->client->request('typo3content/_search?q=*:*');
        $this->assertTrue($response->isOK(), 'Elastica did not answer with ok code.');
        $this->assertSame($response->getData()['hits']['total'], 0, 'Not exactly 0 document was indexed.');
    }

    /**
     * @test
     * @expectedException \Elastica\Exception\ResponseException
     */
    public function someUnknownOperationDoesNotBreakSomething()
    {
        $dataHandler = new CoreDataHandler();
        $hook = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Hook::class);
        //TODO: this test is senseless, checking an exception not correct, this operation should not do anything!
        $hook->processDatamap_afterDatabaseOperations('something', 'tt_content', 6, [], $dataHandler);

        // Should trigger Exception
        $this->client->request('typo3content/_search?q=*:*');
    }
}
