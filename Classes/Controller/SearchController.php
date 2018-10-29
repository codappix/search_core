<?php

namespace Codappix\SearchCore\Controller;

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

use Codappix\SearchCore\Domain\Model\SearchRequest;
use Codappix\SearchCore\Domain\Search\SearchServiceInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Handling search logic in TYPO3 Frontend.
 */
class SearchController extends ActionController
{
    /**
     * @var SearchServiceInterface
     */
    protected $searchService;

    /**
     * @param SearchServiceInterface $searchService
     */
    public function __construct(SearchServiceInterface $searchService)
    {
        $this->searchService = $searchService;

        parent::__construct();
    }

    /**
     * Allow dynamic properties in search request
     */
    public function initializeResultsAction()
    {
        if (isset($this->settings['searching']['mode'])
            && $this->settings['searching']['mode'] === 'filter'
            && $this->request->hasArgument('searchRequest') === false
        ) {
            $this->request->setArguments(array_merge(
                $this->request->getArguments(),
                ['searchRequest' => $this->objectManager->get(SearchRequest::class)]
            ));
        }

        if ($this->arguments->hasArgument('searchRequest')) {
            $this->arguments->getArgument('searchRequest')->getPropertyMappingConfiguration()
                ->allowAllProperties();
        }
    }

    /**
     * Display results and deliver original request and result to view.
     */
    public function resultsAction(SearchRequest $searchRequest = null)
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
