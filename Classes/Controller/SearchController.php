<?php
namespace Leonmrni\SearchCore\Controller;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
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

use Leonmrni\SearchCore\Domain\Model\SearchRequest;
use Leonmrni\SearchCore\Domain\Search\SearchService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Handling search logic in TYPO3 Frontend.
 */
class SearchController extends ActionController
{
    /**
     * @var SearchService
     */
    protected $searchService;

    /**
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;

        parent::__construct();
    }

    /**
     * Process a search and deliver original request and result to view.
     *
     * @param null|SearchRequest $searchRequest
     */
    public function searchAction(SearchRequest $searchRequest = null)
    {
        $searchResult = null;
        if ($searchRequest !== null) {
            $searchResult = $this->searchService->search($searchRequest);
        }

        $this->view->assignMultiple([
            'searchRequest' => $searchRequest,
            'searchResult' => $searchResult,
        ]);
    }
}
