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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Resolves relations from TCA using TCA.
 *
 * E.g. resolves mm relations, items for selects, group db, etc.
 * Will replace the column with an array of resolved labels.
 */
class RelationResolver implements Singleton
{
    /**
     * @var \TYPO3\CMS\Backend\Form\DataPreprocessor
     * @inject
     */
    protected $formEngine;

    /**
     * Resolve relations for the given record.
     *
     * @param TcaTableService $service
     * @param array $record
     */
    public function resolveRelationsForRecord(TcaTableService $service, array &$record)
    {
        $preprocessedData = $this->formEngine->renderRecordRaw(
            $service->getTableName(),
            $record['uid'],
            $record['pid'],
            $record
        );

        foreach (array_keys($record) as $column) {
            try {
                $config = $service->getColumnConfig($column);
            } catch (InvalidArgumentException $e) {
                // Column is not configured.
                continue;
            }

            if (! $this->isRelation($config)) {
                continue;
            }

            $record[$column] = $this->resolveValue($preprocessedData[$column], $config);
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
     * @param string $value The value from FormEngine to resolve.
     * @param array $config The tca config of the relation.
     *
     * @return array<String>|string
     */
    protected function resolveValue($value, array $config)
    {
        $newValue = [];
        if ($value === '' || $value === '0') {
            return '';
        }

        // Select
        if (strpos($value, '|') !== false) {
            foreach (\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $value) as $value) {
                $value = substr($value, strpos($value, '|') + 1);
                $value = rawurldecode($value);
                $newValue[] = $value;
            }

            return $newValue;
        }

        // Inline
        if (strpos($value, ',') !== false) {
            foreach ($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $config['foreign_table'], 'uid in (' . $value . ')') as $record) {
                $newValue[] = BackendUtility::getRecordTitle($config['foreign_table'], $record);
            }

            return $newValue;
        }

        // Static select items
        if ($config['type'] === 'select' && is_array($config['items'])) {
            foreach ($config['items'] as $item) {
                if ($item[1] === $value) {
                    return LocalizationUtility::translate($item[0], '');
                }
            }
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
