<?php
namespace Leonmrni\SearchCore\Connection\Elasticsearch\TypeMapper;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 *
 */
class TcaMapper implements MapperInterface
{
    /**
     * @var TcaMapper\TcaTableService
     */
    protected $tcaTableService;

    /**
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        $this->tcaTableService = GeneralUtility::makeInstance(
            ObjectManager::class
        )->get(TcaMapper\TcaTableService::class, $tableName);
    }

    public function getMapping()
    {
        $mappings = [];
        foreach ($this->tcaTableService->getColumns() as $column) {
            $columnMapping = $this->getColumnMapping($column);
            if ($columnMapping !== null) {
                $mappings[$column] = $columnMapping;
            }
        }

        return $mappings;
    }

    protected function getColumnMapping($column)
    {
        if ($this->tcaTableService->isColumnDate($column)) {
            return [
                'type' => 'date',
                'format' => 'date_optional_time',
            ];
        }
        if ($this->tcaTableService->isColumnBool($column)) {
            return ['type' => 'boolean'];
        }
        if ($this->tcaTableService->isColumnKeyword($column)) {
            return ['type' => 'keyword'];
        }

        return null;
    }
}
