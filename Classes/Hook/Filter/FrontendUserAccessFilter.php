<?php

namespace Codappix\SearchCore\Hook\Filter;

use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Filter: FrontendUserAccess
 * @package Codappix\SearchCore\Hook\Filter
 */
class FrontendUserAccessFilter
{
    /**
     * @param array $parameters
     * @return void
     */
    public function generate($parameters)
    {
        $this->appendQueryWithAccessFilter($parameters['query'], $parameters['value']);
    }

    /**
     * @param array $query
     * @param string $field
     */
    protected function appendQueryWithAccessFilter(array &$query, string $field)
    {
        $query['query']['bool']['must'][] = [
            'terms' => [$field => $this->getUserGroups()]
        ];
    }

    /**
     * @return array
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

    /**
     * @return FrontendUserAuthentication
     */
    protected function getFrontendUserAuthentication()
    {
        return $GLOBALS['TSFE']->fe_user ?? null;
    }
}
