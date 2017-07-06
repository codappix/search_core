<?php
namespace Codappix\SearchCore\Tests\Functional\Connection\Elasticsearch;

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

use Codappix\SearchCore\Tests\Functional\AbstractFunctionalTestCase as BaseFunctionalTestCase;

/**
 * All functional tests should extend this base class.
 *
 * It will take care of leaving a clean environment for next test.
 */
abstract class AbstractFunctionalTestCase extends BaseFunctionalTestCase
{
    /**
     * @var \Elastica\Client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();

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
