<?php
namespace Codappix\SearchCore\Domain\Index\TcaIndexer;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;

/**
 * Resolves relations from TCA using TCA.
 *
 * E.g. resolves mm relations, items for selects, group db, etc.
 * Will replace the column with an array of resolved labels.
 */
class RelationResolver implements Singleton
{
    /**
     * Resolve relations for the given record.
     *
     * @param TcaTableService $service
     * @param array $record
     */
    public function resolveRelationsForRecord(TcaTableService $service, array &$record) : void
    {
        foreach (array_keys($record) as $column) {
            // TODO: Define / configure fields to exclude?!
            if ($column === 'pid') {
                continue;
            }
            $record[$column] = BackendUtility::getProcessedValueExtra($service->getTableName(), $column, $record[$column], 0, $record['uid']);

            try {
                $config = $service->getColumnConfig($column);

                if ($this->isRelation($config)) {
                    $record[$column] = $this->resolveValue($record[$column], $config);
                }
            } catch (InvalidArgumentException $e) {
                // Column is not configured.
            }
        }
    }

    /**
     * Resolve the given value from TYPO3 API response.
     *
     * @param mixed $value The value from FormEngine to resolve.
     * @param array $tcaColumn The tca config of the relation.
     *
     * @return array<String>|string
     */
    protected function resolveValue($value, array $tcaColumn)
    {
        if ($value === '' || $value === '0') {
            return '';
        }

        if ($tcaColumn['type'] === 'select' || $tcaColumn['type'] === 'group') {
            return $this->resolveForeignDbValue($value);
        }

        return '';
    }

    /**
     * @param array Column config.
     * @return bool
     */
    protected function isRelation(array &$config) : bool
    {
        return isset($config['foreign_table'])
            || (isset($config['renderType']) && $config['renderType'] !== 'selectSingle')
            || (isset($config['internal_type']) && strtolower($config['internal_type']) === 'db')
            ;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function resolveForeignDbValue(string $value) : array
    {
        if ($value === 'N/A') {
            return [];
        }
        return array_map('trim', explode(';', $value));
    }
}
