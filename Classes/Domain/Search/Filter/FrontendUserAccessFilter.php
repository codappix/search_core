<?php

namespace Codappix\SearchCore\Domain\Search\Filter;

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

use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Filter: FrontendUserAccess
 */
class FrontendUserAccessFilter implements SearchFilterInterface
{
    public function add(array $query, array $config, $value): array
    {
        return $this->addAccessFilter($query, $value);
    }

    /**
     * Add simple boolean lookup for filtering on access groups
     */
    protected function addAccessFilter(array $query, string $field): array
    {
        $query['query']['bool']['must'][] = [
            'terms' => [$field => $this->getUserGroups()]
        ];
        return $query;
    }

    /**
     * Get inherited user groups from logged in user or simulated user
     */
    protected function getUserGroups(): array
    {
        $feUser = $this->getFrontendUserAuthentication();
        if ($feUser !== null) {
            // If groups is not yet rendered, make sure the group data are fetched
            if (!isset($feUser->groupData['uid'])) {
                $feUser->fetchGroupData();
            }

            $values = $feUser->groupData['uid'];
            if (!empty($values)) {
                // Add public content with values
                return array_merge([0], $values);
            }
        }

        // Fallback on public content
        return [0];
    }

    protected function getFrontendUserAuthentication(): FrontendUserAuthentication
    {
        return $GLOBALS['TSFE']->fe_user ?? null;
    }
}
