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
    public function processData(array $record, array $configuration) : array
    {
        $all = [];

        $this->addArray($all, $record);
        $all = array_filter($all);
        $record[$configuration['to']] = implode(PHP_EOL, $all);

        return $record;
    }

    protected function addArray(array &$target, array $from)
    {
        foreach ($from as $value) {
            if (is_array($value)) {
                $this->addArray($target, $value);
                continue;
            }

            $target[] = (string) $value;
        }
    }
}
