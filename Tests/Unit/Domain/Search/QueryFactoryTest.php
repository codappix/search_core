<?php
namespace Leonmrni\SearchCore\Tests\Unit\Domain\Search;

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

use Leonmrni\SearchCore\Connection;
use Leonmrni\SearchCore\Domain\Model\SearchRequest;
use Leonmrni\SearchCore\Domain\Search\QueryFactory;
use Leonmrni\SearchCore\Tests\Unit\AbstractUnitTestCase;

class QueryFactoryTest extends AbstractUnitTestCase
{
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new QueryFactory;
    }

    /**
     * @test
     */
    public function creatonOfQueryWorksInGeneral()
    {
        $connection = $this->getMockBuilder(Connection\Elasticsearch::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchRequest = new SearchRequest('SearchWord');

        $query = $this->subject->create($connection, $searchRequest);
        $this->assertInstanceOf(
            \Elastica\Query::class,
            $query,
            'Factory did not create the expected instance.'
        );
    }
}
