<?php
namespace Codappix\SearchCore\Tests\Unit\Connection\Elasticsearch;

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

use Codappix\SearchCore\Connection\Elasticsearch\FacetOption;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class FacetOptionTest extends AbstractUnitTestCase
{
    /**
     * @test
     */
    public function displayNameIsReturnedAsExpected()
    {
        $bucket = [
            'key' => 'Name',
            'key_as_string' => 'DisplayName',
            'doc_count' => 10,
        ];
        $subject = new FacetOption($bucket);

        $this->assertSame(
            $bucket['key_as_string'],
            $subject->getDisplayName(),
            'Display name was not returned as expected.'
        );
    }

    /**
     * @test
     */
    public function displayNameIsReturnedAsExpectedIfNotProvided()
    {
        $bucket = [
            'key' => 'Name',
            'doc_count' => 10,
        ];
        $subject = new FacetOption($bucket);

        $this->assertSame(
            $bucket['key'],
            $subject->getDisplayName(),
            'Display name was not returned as expected.'
        );
    }
}
