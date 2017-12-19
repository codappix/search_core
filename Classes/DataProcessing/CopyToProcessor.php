<?php
namespace Codappix\SearchCore\DataProcessing;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Copies values from one field to another one.
 */
class CopyToProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    protected $keysToCopy = [];

    public function processRecord(array $record, array $configuration)
    {
        $result = [];
        $this->keysToCopy = array_keys($record);

        if (isset($configuration['from'])) {
            $this->keysToCopy = GeneralUtility::trimExplode(',', $configuration['from']);
        }

        $this->addArray($result, $record);
        $result = array_filter($result);
        $record[$configuration['to']] = implode(PHP_EOL, $result);

        return $record;
    }

    /**
     * @param array &$to
     * @param array $from
     */
    protected function addArray(array &$to, array $from)
    {
        foreach ($from as $property => $value) {
            if (!in_array($property, $this->keysToCopy)) {
                continue;
            }
            if (is_array($value)) {
                $this->addArray($to, $value);
                continue;
            }

            $to[] = (string) $value;
        }
    }
}
