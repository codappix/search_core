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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Domain\Index\IndexingException;
use Codappix\SearchCore\Domain\Index\TcaIndexer\InvalidArgumentException;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Encapsulate logik related to TCA configuration.
 */
class TcaTableService76 implements TcaTableServiceInterface
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
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @var RelationResolver
     */
    protected $relationResolver;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Inject log manager to get concrete logger from it.
     *
     * @param \TYPO3\CMS\Core\Log\LogManager $logManager
     */
    public function injectLogger(\TYPO3\CMS\Core\Log\LogManager $logManager)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
    }

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $tableName
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(
        $tableName,
        RelationResolver $relationResolver,
        ConfigurationContainerInterface $configuration
    ) {
        if (!isset($GLOBALS['TCA'][$tableName])) {
            throw new IndexingException(
                'Table "' . $tableName . '" is not configured in TCA.',
                IndexingException::CODE_UNKOWN_TCA_TABLE
            );
        }

        $this->tableName = $tableName;
        $this->tca = &$GLOBALS['TCA'][$this->tableName];
        $this->configuration = $configuration;
        $this->relationResolver = $relationResolver;
    }

    public function getTableName() : string
    {
        return $this->tableName;
    }

    public function getTableClause() : string
    {
        if ($this->tableName === 'pages') {
            return $this->tableName;
        }

        return $this->tableName . ' LEFT JOIN pages on ' . $this->tableName . '.pid = pages.uid';
    }

    public function getRecords(int $offset, int $limit) : array
    {
        $records = $this->getConnection()->exec_SELECTgetRows(
            $this->getFields(),
            $this->getTableClause(),
            $this->getWhereClause(),
            '',
            '',
            (int) $offset . ',' . (int) $limit
        );

        return $records ?: [];
    }

    public function getRecord(int $identifier) : array
    {
        $record = $this->getConnection()->exec_SELECTgetSingleRow(
            $this->getFields(),
            $this->getTableClause(),
            $this->getWhereClause()
            . ' AND ' . $this->getTableName() . '.uid = ' . (int) $identifier
        );

        return $record ?: [];
    }

    public function filterRecordsByRootLineBlacklist(array &$records)
    {
        $records = array_filter(
            $records,
            function ($record) {
                return ! $this->isRecordBlacklistedByRootline($record);
            }
        );
    }

    public function prepareRecord(array &$record)
    {
        $this->relationResolver->resolveRelationsForRecord($this, $record);

        if (isset($record['uid']) && !isset($record['search_identifier'])) {
            $record['search_identifier'] = $record['uid'];
        }
        if (isset($record[$this->tca['ctrl']['label']]) && !isset($record['search_title'])) {
            $record['search_title'] = $record[$this->tca['ctrl']['label']];
        }
    }

    public function getWhereClause() : string
    {
        $whereClause = '1=1'
            . BackendUtility::BEenableFields($this->tableName)
            . BackendUtility::deleteClause($this->tableName)
            . ' AND pages.no_search = 0'
            ;

        if ($this->tableName !== 'pages') {
            $whereClause .= BackendUtility::BEenableFields('pages')
                . BackendUtility::deleteClause('pages')
                ;
        }

        $userDefinedWhere = $this->configuration->getIfExists(
            'indexing.' . $this->getTableName() . '.additionalWhereClause'
        );
        if (is_string($userDefinedWhere)) {
            $whereClause .= ' AND ' . $userDefinedWhere;
        }
        if ($this->isBlacklistedRootLineConfigured()) {
            $whereClause .= ' AND pages.uid NOT IN ('
                . implode(',', $this->getBlacklistedRootLine())
                . ')'
                . ' AND pages.pid NOT IN ('
                . implode(',', $this->getBlacklistedRootLine())
                . ')';
        }

        $this->logger->debug('Generated where clause.', [$this->tableName, $whereClause]);
        return $whereClause;
    }

    public function getFields() : string
    {
        $fields = array_merge(
            ['uid','pid'],
            array_filter(
                array_keys($this->tca['columns']),
                function ($columnName) {
                    return !$this->isSystemField($columnName)
                        && !$this->isUserField($columnName)
                        && !$this->isPassthroughField($columnName)
                        ;
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
     * Generate SQL for TYPO3 as a system, to make sure only available records
     * are fetched.
     */
    protected function getSystemWhereClause() : string
    {
        $whereClause = '1=1'
            . BackendUtility::BEenableFields($this->tableName)
            . BackendUtility::deleteClause($this->tableName)
            . ' AND pages.no_search = 0'
            ;

        if ($this->tableName !== 'pages') {
            $whereClause .= BackendUtility::BEenableFields('pages')
                . BackendUtility::deleteClause('pages')
                ;
        }

        return $whereClause;
    }

    protected function isSystemField(string $columnName) : bool
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
            $this->tca['ctrl']['origUid'],
        ];

        return in_array($columnName, $systemFields);
    }

    protected function isUserField(string $columnName) : bool
    {
        $config = $this->getColumnConfig($columnName);
        return isset($config['type']) && $config['type'] === 'user';
    }

    protected function isPassthroughField(string $columnName) : bool
    {
        $config = $this->getColumnConfig($columnName);
        return isset($config['type']) && $config['type'] === 'passthrough';
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getColumnConfig(string $columnName) : array
    {
        if (!isset($this->tca['columns'][$columnName])) {
            throw new InvalidArgumentException(
                'Column does not exist.',
                InvalidArgumentException::COLUMN_DOES_NOT_EXIST
            );
        }

        return $this->tca['columns'][$columnName]['config'];
    }

    public function getLanguageUidColumn() : string
    {
        if (!isset($this->tca['ctrl']['languageField'])) {
            return '';
        }

        return $this->tca['ctrl']['languageField'];
    }

    /**
     * Checks whether the given record was blacklisted by root line.
     * This can be configured by typoscript as whole root lines can be black listed.
     *
     * Also further TYPO3 mechanics are taken into account. Does a valid root
     * line exist, is page inside a recycler, is inherited start- endtime
     * excluded, etc.
     */
    protected function isRecordBlacklistedByRootline(array &$record) : bool
    {
        $pageUid = $record['pid'];
        if ($this->tableName === 'pages') {
            $pageUid = $record['uid'];
        }

        try {
            $rootline = $this->objectManager->get(RootlineUtility::class, $pageUid)->get();
        } catch (\RuntimeException $e) {
            $this->logger->notice(
                sprintf('Could not fetch rootline for page %u, because: %s', $pageUid, $e->getMessage()),
                [$record, $e]
            );
            return true;
        }

        foreach ($rootline as $pageInRootLine) {
            // Check configured black list if present.
            if ($this->isBlackListedRootLineConfigured()
                && in_array($pageInRootLine['uid'], $this->getBlackListedRootLine())
            ) {
                $this->logger->info(
                    sprintf(
                        'Record %u is black listed due to configured root line configuration of page %u.',
                        $record['uid'],
                        $pageInRootLine['uid']
                    ),
                    [$record, $pageInRootLine]
                );
                return true;
            }

            if ($pageInRootLine['extendToSubpages'] && (
                ($pageInRootLine['endtime'] > 0 && $pageInRootLine['endtime'] <= time())
                || ($pageInRootLine['starttime'] > 0 && $pageInRootLine['starttime'] >= time())
            )) {
                $this->logger->info(
                    sprintf(
                        'Record %u is black listed due to configured timing of parent page %u.',
                        $record['uid'],
                        $pageInRootLine['uid']
                    ),
                    [$record, $pageInRootLine]
                );
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether any page uids are black listed.
     */
    protected function isBlackListedRootLineConfigured() : bool
    {
        return (bool) $this->configuration->getIfExists('indexing.' . $this->getTableName() . '.rootLineBlacklist');
    }

    /**
     * Get the list of black listed root line page uids.
     *
     * @return array<Int>
     */
    protected function getBlackListedRootLine() : array
    {
        return GeneralUtility::intExplode(
            ',',
            $this->configuration->getIfExists('indexing.' . $this->getTableName() . '.rootLineBlacklist')
        );
    }

    protected function getConnection() : \TYPO3\CMS\Core\Database\DatabaseConnection
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
