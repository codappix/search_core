<?php
namespace Codappix\SearchCore\Domain\Index\TcaIndexer;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
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
use Codappix\SearchCore\Connection\ConnectionInterface;
use Codappix\SearchCore\Domain\Index\TcaIndexer;

/**
 * Specific indexer for Pages, will basically add content of page.
 */
class PagesIndexer extends TcaIndexer
{
    /**
     * @var TcaTableService
     */
    protected $contentTableService;

    /**
     * @param TcaTableService $tcaTableService
     * @param TcaTableService $tcaTableService
     * @param ConnectionInterface $connection
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(
        TcaTableService $tcaTableService,
        TcaTableService $contentTableService,
        ConnectionInterface $connection,
        ConfigurationContainerInterface $configuration
    ) {
        $this->tcaTableService = $tcaTableService;
        $this->contentTableService = $contentTableService;
        $this->connection = $connection;
        $this->configuration = $configuration;
    }

    /**
     * @param array &$record
     */
    protected function prepareRecord(array &$record)
    {
        parent::prepareRecord($record);
        $record['content'] = $this->fetchContentForPage($record['uid']);
    }

    /**
     * @param int $uid
     * @return string
     */
    protected function fetchContentForPage($uid)
    {
        $contentElements = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            $this->contentTableService->getFields(),
            $this->contentTableService->getTableClause(),
            $this->contentTableService->getWhereClause() .
                sprintf(' AND %s.pid = %u', $this->contentTableService->getTableName(), $uid)
        );

        if ($contentElements === null) {
            $this->logger->debug('No content for page ' . $uid);
            return '';
        }

        $this->logger->debug('Fetched content for page ' . $uid);
        $content = [];
        foreach ($contentElements as $contentElement) {
            $content[] = $contentElement['bodytext'];
        }

        // Remove Tags.
        // Interpret escaped new lines and special chars.
        // Trim, e.g. trailing or leading new lines.
        return trim(stripcslashes(strip_tags(implode(' ', $content))));
    }
}
