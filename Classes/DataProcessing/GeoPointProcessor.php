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
 * Adds a new fields, ready to use as GeoPoint field for Elasticsearch.
 */
class GeoPointProcessor implements ProcessorInterface
{
    public function processData(array $record, array $configuration) : array
    {
        if (! $this->isApplyable($record, $configuration)) {
            return $record;
        }

        $record[$configuration['to']] = [
            'lat' => (float) $record[$configuration['lat']],
            'lon' => (float) $record[$configuration['lon']],
        ];

        return $record;
    }

    protected function isApplyable(array $record, array $configuration) : bool
    {
        if (!isset($record[$configuration['lat']])
            || !is_numeric($record[$configuration['lat']])
            || trim($record[$configuration['lat']]) === ''
        ) {
            return false;
        }
        if (!isset($record[$configuration['lon']])
            || !is_numeric($record[$configuration['lon']])
            || trim($record[$configuration['lon']]) === ''
        ) {
            return false;
        }

        return true;
    }
}
