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

use Codappix\SearchCore\Utility\FrontendUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Resolves relations from TCA using TCA.
 *
 * E.g. resolves mm relations, items for selects, group db, etc.
 * Will replace the column with an array of resolved labels.
 */
class RelationResolver implements Singleton
{
    /**
     * @param TcaTableServiceInterface $service
     * @param array $record
     * @return array
     */
    public function resolveRelationsForRecord(TcaTableServiceInterface $service, array $record): array
    {
        foreach (array_keys($record) as $column) {
            if (in_array($column, ['pid', $service->getLanguageUidColumn()])) {
                $record[$column] = (int)$record[$column];
                continue;
            }

            $record[$column] = $this->getColumnValue($record, $column, $service);

            try {
                $config = $service->getColumnConfig($column);

                if ($this->isRelation($config)) {
                    $record[$column] = $this->resolveValue($record[$column], $config);
                }
            } catch (InvalidArgumentException $e) {
                // Column is not configured.
                continue;
            }
        }

        return $record;
    }

    /**
     * @param string $value
     * @param array $tcaColumn
     * @return array
     */
    protected function resolveValue($value, array $tcaColumn)
    {
        if ($value === '' || $value === 'N/A') {
            return [];
        }

        if ($tcaColumn['type'] === 'select' && strpos($value, ';') !== false) {
            return $this->resolveForeignDbValue($value);
        }
        if (in_array($tcaColumn['type'], ['inline', 'group', 'select'])) {
            return $this->resolveInlineValue($value);
        }

        return [];
    }

    /**
     * @param array $config
     * @return boolean
     */
    protected function isRelation(array &$config): bool
    {
        return isset($config['foreign_table'])
            || (isset($config['renderType']) && !in_array($config['renderType'], ['selectSingle', 'inputDateTime']))
            || (isset($config['internal_type']) && strtolower($config['internal_type']) === 'db');
    }

    /**
     * @param string $value
     * @return array
     */
    protected function resolveForeignDbValue(string $value): array
    {
        return array_map('trim', explode(';', $value));
    }

    /**
     * @param string $value
     * @return array
     */
    protected function resolveInlineValue(string $value): array
    {
        return array_map('trim', explode(',', $value));
    }

    /**
     * @return string
     */
    protected function getUtilityForMode(): string
    {
        if (TYPO3_MODE === 'BE') {
            return BackendUtility::class;
        }

        return FrontendUtility::class;
    }

    /**
     * @param array $record
     * @param string $column
     * @param TcaTableServiceInterface $service
     * @return string
     */
    protected function getColumnValue(array $record, string $column, TcaTableServiceInterface $service): string
    {
        $utility = GeneralUtility::makeInstance($this->getUtilityForMode());
        return $utility::getProcessedValueExtra(
                $service->getTableName(),
                $column,
                $record[$column],
                0,
                $record['uid']
            ) ?? '';
    }
}
