<?php
namespace Leonmrni\SearchCore\Domain\Index\TcaIndexer;

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
    public function resolveRelationsForRecord(TcaTableService $service, array &$record)
    {
        $formData = GeneralUtility::makeInstance(FormDataCompiler::class, GeneralUtility::makeInstance(TcaDatabaseRecord::class))
            ->compile([
                'tableName' => $service->getTableName(),
                'vanillaUid' => (int)$record['uid'],
                'command' => 'edit',
            ]);

        $record = $formData['databaseRow'];

        foreach (array_keys($record) as $column) {
            try {
                $config = $service->getColumnConfig($column);
            } catch (InvalidArgumentException $e) {
                // Column is not configured.
                continue;
            }

            if (! $this->isRelation($config) || !is_array($formData['processedTca']['columns'][$column])) {
                continue;
            }

            $record[$column] = $this->resolveValue($record[$column], $formData['processedTca']['columns'][$column]);
        }
    }

    /**
     * Resolve the given value from TYPO3 API response.
     *
     * @param string $value The value from FormEngine to resolve.
     * @param array $tcaColumn The tca config of the relation.
     *
     * @return array<String>|string
     */
    protected function resolveValue($value, array $tcaColumn)
    {
        if ($value === '' || $value === '0') {
            return '';
        }
        if ($tcaColumn['config']['type'] === 'select') {
            return $this->resolveSelectValue($value, $tcaColumn);
        }
        if ($tcaColumn['config']['type'] === 'group' && strpos($value, '|') !== false) {
            return $this->resolveForeignDbValue($value);
        }
        if ($tcaColumn['config']['type'] === 'inline') {
            return $this->resolveInlineValue($tcaColumn);
        }

        return '';
    }

    /**
     * @param array Column config.
     * @return bool
     */
    protected function isRelation(array &$config)
    {
        return isset($config['foreign_table'])
            || (isset($config['items']) && is_array($config['items']))
            || (isset($config['internal_type']) && strtolower($config['internal_type']) === 'db')
            ;
    }

    /**
     * Resolves internal representation of select to array of labels.
     *
     * @param array $value
     * @param array $tcaColumn
     * @return array
     */
    protected function resolveSelectValue(array $values, array $tcaColumn)
    {
        $resolvedValues = [];

        foreach ($tcaColumn['config']['items'] as $item) {
            if (in_array($item[1], $values)) {
                $resolvedValues[] = $item[0];
            }
        }

        if ($tcaColumn['config']['renderType'] === 'selectSingle' || $tcaColumn['config']['maxitems'] === 1) {
            return current($resolvedValues);
        }

        return $resolvedValues;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function resolveForeignDbValue($value)
    {
        $titles = [];

        foreach (explode(',', urldecode($value)) as $title) {
            $titles[] = explode('|', $title)[1];
        }

        return $titles;
    }

    /**
     * @param array $tcaColumn
     * @return array
     */
    protected function resolveInlineValue(array $tcaColumn)
    {
        $titles = [];

        foreach ($tcaColumn['children'] as $selected) {
            $titles[] = $selected['recordTitle'];
        }

        return $titles;
    }
}
