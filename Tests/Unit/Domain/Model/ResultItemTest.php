<?php
namespace Codappix\SearchCore\Tests\Unit\Domain\Model;

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

use Codappix\SearchCore\Domain\Model\ResultItem;
use Codappix\SearchCore\Tests\Unit\AbstractUnitTestCase;

class ResultItemTest extends AbstractUnitTestCase
{
    /**
     * @test
     */
    public function plainDataCanBeRetrieved()
    {
        $originalData = [
            'uid' => 10,
            'title' => 'Some title',
        ];
        $expectedData = $originalData;

        $subject = new ResultItem($originalData);
        $this->assertSame(
            $expectedData,
            $subject->getPlainData(),
            'Could not retrieve plain data from result item.'
        );
    }

    /**
     * @test
     */
    public function dataCanBeRetrievedInArrayNotation()
    {
        $originalData = [
            'uid' => 10,
            'title' => 'Some title',
        ];
        $expectedData = $originalData;

        $subject = new ResultItem($originalData);
        $this->assertSame(
            'Some title',
            $subject['title'],
            'Could not retrieve title in array notation.'
        );
    }

    /**
     * @test
     */
    public function existenceOfDataCanBeChecked()
    {
        $originalData = [
            'uid' => 10,
            'title' => 'Some title',
        ];
        $expectedData = $originalData;

        $subject = new ResultItem($originalData);
        $this->assertTrue(isset($subject['title']), 'Could not determine that title exists.');
        $this->assertFalse(isset($subject['title2']), 'Could not determine that title2 does not exists.');
    }

    /**
     * @test
     */
    public function dataCanNotBeChanged()
    {
        $originalData = [
            'uid' => 10,
            'title' => 'Some title',
        ];
        $expectedData = $originalData;

        $subject = new ResultItem($originalData);
        $this->expectException(\BadMethodCallException::class);
        $subject['title'] = 'New Title';
    }

    /**
     * @test
     */
    public function dataCanNotBeRemoved()
    {
        $originalData = [
            'uid' => 10,
            'title' => 'Some title',
        ];
        $expectedData = $originalData;

        $subject = new ResultItem($originalData);
        $this->expectException(\BadMethodCallException::class);
        unset($subject['title']);
    }
}
