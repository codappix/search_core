<?php
namespace Leonmrni\SearchCore\Connection\Elasticsearch\TypeMapper\TcaMapper;

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

/**
 * Encapsulates TCA structure for use in other classes like TcaMapper.
 */
class TcaTableService
{
    /**
     * TCA for current table.
     * !REFERENCE! To save memory.
     * @var array
     */
    protected $tca;

    /**
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        if (!isset($GLOBALS['TCA'][$tableName])) {
            throw new \Exception(
                'Table "' . $tableName . '" is not configured in TCA.',
                1482244962
            );
        }

        $this->tca = &$GLOBALS['TCA'][$tableName];
    }

    public function getColumns()
    {
        return array_keys($this->tca['columns']);
    }

    public function isColumnKeyword($column)
    {
        return $this->tca['ctrl']['type'] === $column;
    }

    public function isColumnDate($column)
    {
        return $this->tca['ctrl']['enablecolumns']['starttime'] === $column
            || $this->tca['ctrl']['enablecolumns']['endtime'] === $column
            || $this->tca['ctrl']['crdate'] === $column
            || $this->tca['ctrl']['tstamp'] === $column
            || (
                isset($this->tca['columns'][$column]['config']['eval'])
                && stripos($this->tca['columns'][$column]['config']['eval'], 'datetime') !== false
            )
            || (
                isset($this->tca['columns'][$column]['config']['eval'])
                && stripos($this->tca['columns'][$column]['config']['eval'], 'date') !== false
            )
            || (
                isset($this->tca['columns'][$column]['config']['eval'])
                && stripos($this->tca['columns'][$column]['config']['eval'], 'time') !== false
            )
        ;
    }

    public function isColumnBool($column)
    {
        return $this->tca['ctrl']['delete'] === $column
            || $this->tca['ctrl']['enablecolumns']['disabled'] === $column
        ;
    }
}
