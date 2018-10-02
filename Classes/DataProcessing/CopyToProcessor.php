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

/**
 * Copies values from one field to another one.
 */
class CopyToProcessor implements ProcessorInterface
{
    /**
     * @param array $record
     * @param array $configuration
     * @return array
     */
    public function processData(array $record, array $configuration): array
    {
        $target = [];

        $from = $record;
        if (isset($configuration['from'])) {
            $from = $record[$configuration['from']];
        }

        if (is_array($from)) {
            $this->addArray($target, $from);
        } else {
            $target[] = (string)$from;
        }

        $target = array_filter($target);
        $record[$configuration['to']] = implode(PHP_EOL, $target);

        return $record;
    }

    /**
     * @param array $target
     * @param array $from
     * @return void
     */
    protected function addArray(array &$target, array $from)
    {
        foreach ($from as $value) {
            if (is_array($value)) {
                $this->addArray($target, $value);
                continue;
            }

            $target[] = (string)$value;
        }
    }
}
