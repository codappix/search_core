Breaking Change "Changed interfaces"
====================================

Some interfaces and abstract classes have been adjusted:

``Codappix\SearchCore\Connection\ConnectionInterface``:

   * New method ``public function deleteIndexByDocumentType(string $documentType);``

``Codappix\SearchCore\Domain\Index\IndexerInterface``:

   * New method ``public function deleteDocuments();``

   * New method ``public function getIdentifier(): string;``

``Codappix\SearchCore\Domain\Index\AbstractIndexer``:

   * New method ``abstract public function getDocumentIdentifier($identifier): string;``

``Codappix\SearchCore\Connection\SearchRequestInterface``:

   * Changed method signature from ``public function setSearchService(SearchService $searchService);``
     to ``public function setSearchService(SearchServiceInterface $searchService);``

Also some exceptions have changed:

* ``Codappix\SearchCore\Connection\Elasticsearch\DocumentFactory::getDocument()`` now
throws an ``\InvalidArgumentException`` instead of ``\Exception``, if no
``search_identifier`` was provided.


