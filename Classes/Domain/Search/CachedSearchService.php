<?php

namespace Codappix\SearchCore\Domain\Search;

use Codappix\SearchCore\Connection\SearchRequestInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Service: Cached Search
 * @package Codappix\SearchCore\Domain\Search
 */
class CachedSearchService implements SingletonInterface
{
    protected $results = [];
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
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $searchRequest): SearchResultInterface
    {
        $hash = $this->getHash($searchRequest);
        if (isset($this->results[$hash]) && $this->results[$hash] instanceof SearchResultInterface) {
            return $this->results[$hash];
        }
        return $this->results[$hash] = $this->searchService->search($searchRequest);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return string
     */
    protected function getHash(SearchRequestInterface $searchRequest): string
    {
        if (is_callable([$searchRequest, 'getRequestHash'])) {
            return (string)$searchRequest->getRequestHash();
        }
        return sha1(serialize($searchRequest));
    }

}
