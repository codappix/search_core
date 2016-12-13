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
use Leonmrni\SearchCore\Domain\Index\IndexingException;

/**
 * Encapsulate logik related to tca configuration.
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
     * @var string
     */
    protected $tableName;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * Inject log manager to get concrete logger from it.
     *
     * @param \TYPO3\CMS\Core\Log\LogManager $logManager
     */
    public function injectLogger(\TYPO3\CMS\Core\Log\LogManager $logManager)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
    }

    public function __construct($tableName)
    {
        if (!isset($GLOBALS['TCA'][$tableName])) {
            throw new IndexingException(
                'Table "' . $tableName . '" is not configured in TCA.',
                IndexingException::CODE_UNKOWN_TCA_TABLE
            );
        }

        $this->tableName = $tableName;
        $this->tca = &$GLOBALS['TCA'][$this->tableName];
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getTableClause()
    {
        return $this->tableName . ' LEFT JOIN pages on ' . $this->tableName . '.pid = pages.uid';
    }

    /**
     * Adjust record accordingly to configuration.
     * @param array &$record
     */
    public function prepareRecord(array &$record)
    {
        // TODO: Resolve values from 'items' like static select, radio or checkbox.

        if (isset($record['uid']) && !isset($record['search_identifier'])) {
            $record['search_identifier'] = $record['uid'];
        }
        if (isset($record[$this->tca['ctrl']['label']]) && !isset($record['search_title'])) {
            $record['search_title'] = $record[$this->tca['ctrl']['label']];
        }
    }

    /**
     * @return string
     */
    public function getWhereClause()
    {
        $whereClause = '1=1 '
            . BackendUtility::BEenableFields($this->tableName)
            . BackendUtility::deleteClause($this->tableName)

            . BackendUtility::BEenableFields('pages')
            . BackendUtility::deleteClause('pages')
            . ' AND pages.no_search = 0'
            ;

        $this->logger->debug('Generated where clause.', [$this->tableName, $whereClause]);
        return $whereClause;
    }

    /**
     * @return string
     */
    public function getFields()
    {
        $fields = array_merge(
            ['uid','pid'],
            array_filter(
                array_keys($this->tca['columns']),
                function ($columnName) {
                    $columnConfig = $this->tca['columns'][$columnName]['config'];
                    return !$this->isRelation($columnConfig) && !$this->isSystemField($columnName);
                }
            )
        );

        foreach ($fields as $key => $field) {
            $fields[$key] = $this->tableName . '.' . $field;
        }

        $this->logger->debug('Generated fields.', [$this->tableName, $fields]);
        return implode(',', $fields);
    }

    /**
     * @param array
     * @return bool
     */
    protected function isRelation(array &$columnConfig)
    {
        return isset($columnConfig['foreign_table']);
    }

    /**
     * @param string
     * @return bool
     */
    protected function isSystemField($columnName)
    {
        $systemFields = [
            // Versioning fields,
            // https://docs.typo3.org/typo3cms/TCAReference/Reference/Ctrl/Index.html#versioningws
            't3ver_oid', 't3ver_id', 't3ver_label', 't3ver_wsid',
            't3ver_state', 't3ver_stage', 't3ver_count', 't3ver_tstamp',
            't3ver_move_id', 't3ver_swapmode',
            $this->tca['ctrl']['transOrigDiffSourceField'],
            $this->tca['ctrl']['cruser_id'],
            $this->tca['ctrl']['fe_cruser_id'],
            $this->tca['ctrl']['fe_crgroup_id'],
            $this->tca['ctrl']['languageField'],
            $this->tca['ctrl']['origUid'],
        ];

        return in_array($columnName, $systemFields);
    }
}
