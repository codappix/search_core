<?php
namespace Leonmrni\SearchCore\Tests\Functional;

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

use TYPO3\CMS\Core\Tests\FunctionalTestCase as CoreTestCase;

/**
 * All functional tests should extend this base class.
 *
 * It will take care of leaving a clean environment for next test.
 */
abstract class FunctionalTestCase extends CoreTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/search_core'];

    /**
     * @var \Elastica\Client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();

        // Provide necessary configuration for extension
        $this->importDataSet('Tests/Functional/Fixtures/BasicSetup.xml');
        $this->setUpFrontendRootPage(1, ['EXT:search_core/Tests/Functional/Fixtures/BasicSetup.ts']);

        // Create client to make requests and assert something.
        $this->client = new \Elastica\Client([
            'host' => getenv('ES_HOST') ?: \Elastica\Connection::DEFAULT_HOST,
            'port' => getenv('ES_PORT') ?: \Elastica\Connection::DEFAULT_PORT,
        ]);
    }

    public function tearDown()
    {
        // Delete everything so next test starts clean.
        $this->client->getIndex('_all')->delete();
        $this->client->getIndex('_all')->clearCache();
    }
}
