<?php

namespace Codappix\SearchCore\DataProcessing;

/*
 * Copyright (C) 2018  Daniel Siepmann <coding@daniel-siepmann.de>
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

use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Eases work with data processing.
 */
class Service
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Service constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Executes the dataprocessor depending on configuration and returns the result.
     *
     * @param array|string $configuration Either the full configuration or only the class name.
     * @param array $data
     * @param string $recordType
     * @return array
     */
    public function executeDataProcessor($configuration, array $data, string $recordType = ''): array
    {
        if (is_string($configuration)) {
            $configuration = [
                '_typoScriptNodeValue' => $configuration,
            ];
        }

        if ($recordType !== '' && !isset($configuration['_table'])) {
            $configuration['_table'] = $recordType;
        }

        return $this->objectManager->get($configuration['_typoScriptNodeValue'])
            ->processData($data, $configuration);
    }
}
