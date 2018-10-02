<?php

namespace Codappix\SearchCore\Integration\Form\Finisher;

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

use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;

/**
 * Integrates search_core indexing into TYPO3 Form extension.
 *
 * Add this finisher AFTER all database operations, as search_core will fetch
 * information from database.
 */
class DataHandlerFinisher extends AbstractFinisher
{
    /**
     * @var \Codappix\SearchCore\Domain\Service\DataHandler
     * @inject
     */
    protected $dataHandler;

    /**
     * @var array
     */
    protected $defaultOptions = [
        'indexIdentifier' => null,
        'recordUid' => null,
        'action' => '',
    ];

    /**
     * @return null|string|void
     * @throws FinisherException
     * @throws \Codappix\SearchCore\Domain\Index\NoMatchingIndexerException
     */
    protected function executeInternal()
    {
        $action = $this->parseOption('action');
        $record = ['uid' => (int)$this->parseOption('recordUid')];
        $tableName = $this->parseOption('indexIdentifier');

        if ($action === '' || $tableName === '' || !is_string($tableName) || $record['uid'] === 0) {
            throw new FinisherException('Not all necessary options were set.', 1510313095);
        }

        switch ($action) {
            case 'update':
            case 'add':
                $this->dataHandler->update($tableName, $record);
                break;
            case 'delete':
                $this->dataHandler->delete($tableName, (string)$record['uid']);
                break;
        }
    }
}
