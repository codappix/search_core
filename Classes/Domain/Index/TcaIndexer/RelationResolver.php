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

use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow;
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
     * Resolve relations for the given record.
     *
     * @param TcaTableService $service
     * @param array $record
     */
    public function resolveRelationsForRecord(TcaTableService $service, array &$record)
    {
        $formDataGroup = GeneralUtility::makeInstance(TcaDatabaseRecord::class);
        // $formDataGroup->setProviderList([ DatabaseEditRow::class ]);
        /** @var FormDataCompiler $formDataCompiler */
        $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
        $input = [
            'tableName' => $service->getTableName(),
            'vanillaUid' => (int)$record['uid'],
            'command' => 'edit',
        ];
        $result = $formDataCompiler->compile($input);

        foreach (array_keys($record) as $column) {
            if (! isset($result['processedTca']['columns'][$column])
                || ! $this->isRelation($result['processedTca']['columns'][$column]['config'])
            ) {
                continue;
            }

            $value = $record[$column];

            if (isset($result['processedTca']['columns'][$column]['config']['treeData']['selectedNodes'])) {
                $value = $this->resolveSelectValue($result['processedTca']['columns'][$column]['config']['treeData']['selectedNodes']);
            }

            $record[$column] = $value;
        }
    }

    /**
     * Resolve the given value from TYPO3 API response.
     *
     * As FormEngine uses an internal format, we resolve it to a usable format
     * for indexing.
     *
     * TODO: Unittest to break as soon as TYPO3 api has changed, so we know
     * exactly that we need to tackle this place.
     *
     * @param array<String> $values
     *
     * @return array<String>|string
     */
    protected function resolveSelectValue(array $values)
    {
        $newValue = [];
        foreach ($values as $value) {
            $value = substr($value, strpos($value, '|') + 1);
            $value = rawurldecode($value);
            $newValue[] = $value;
        }
        return $newValue;
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
}
