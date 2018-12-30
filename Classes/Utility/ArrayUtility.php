<?php

namespace Codappix\SearchCore\Utility;

/*
 * Copyright (C) 2018 Benjamin Serfhos <benjamin@serfhos.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Core\Utility\ArrayUtility as Typo3ArrayUtility;

class ArrayUtility extends Typo3ArrayUtility
{
    /**
     * Recursively removes empty array elements.
     *
     * @see \TYPO3\CMS\Extbase\Utility\ArrayUtility::removeEmptyElementsRecursively Removed in TYPO3 v9
     */
    public static function removeEmptyElementsRecursively(array $array): array
    {
        $result = $array;
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::removeEmptyElementsRecursively($value);
                if ($result[$key] === []) {
                    unset($result[$key]);
                }
            } elseif ($value === null) {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
