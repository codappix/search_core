Feature "SearchServiceInterface"
================================

The concrete search service can now be exchanged. Therefore a new Interface was
introduced::

   namespace Codappix\SearchCore\Domain\Search;

   use Codappix\SearchCore\Connection\SearchRequestInterface;
   use Codappix\SearchCore\Connection\SearchResultInterface;

   interface SearchServiceInterface
   {
      /**
      * Fetches result for provided search request.
      */
      public function search(SearchRequestInterface $searchRequest): SearchResultInterface;

      /**
      * Processes the result, e.g. applies configured data processing to result.
      */
      public function processResult(SearchResultInterface $searchResult): SearchResultInterface;
   }
