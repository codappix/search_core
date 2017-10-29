<?php
namespace Codappix\SearchCore\Configuration;

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

use Codappix\SearchCore\Connection\SearchRequestInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ConfigurationUtility
{
    /**
     * Will parse all entries, recursive as fluid template, with request variable set to $searchRequest.
     */
    public function replaceArrayValuesWithRequestContent(SearchRequestInterface $searchRequest, array $array) : array
    {
        array_walk_recursive($array, function (&$value, $key, SearchRequestInterface $searchRequest) {
            $template = new StandaloneView();
            $template->assign('request', $searchRequest);
            $template->setTemplateSource($value);
            $value = $template->render();

            // As elasticsearch does need some doubles to be send as doubles.
            if (is_numeric($value)) {
                $value = (float) $value;
            }
        }, $searchRequest);

        return $array;
    }

    /**
     * Will check all entries, whether they have a condition and filter entries out, where condition is false.
     * Also will remove condition in the end.
     */
    public function filterByCondition(array $entries) : array
    {
        $entries = array_filter($entries, function ($entry) {
            return !is_array($entry)
                || !array_key_exists('condition', $entry)
                || (bool) $entry['condition'] === true
                ;
        });

        foreach ($entries as $key => $entry) {
            if (is_array($entry) && array_key_exists('condition', $entry)) {
                unset($entries[$key]['condition']);
            }
        }

        return $entries;
    }
}
