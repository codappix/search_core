Breaking Change "Changed interfaces"
====================================

Some interfaces and abstract classes have been adjusted:

``Codappix\SearchCore\Connection\ConnectionInterface``:

   * New method ``public function deleteAllDocuments(string $documentType);``

``Codappix\SearchCore\Domain\Index\IndexerInterface``:

   * New method ``public function deleteDocuments();``

   * New method ``public function getIdentifier(): string;``

``Codappix\SearchCore\Domain\Index\AbstractIndexer``:

   * New method ``abstract protected function getDocumentIdentifier($identifier): string;``

``Codappix\SearchCore\Connection\SearchRequestInterface``:

   * Changed method signature from ``public function setSearchService(SearchService $searchService);``
     to ``public function setSearchService(SearchServiceInterface $searchService);``

Also some exceptions have changed:

* ``Codappix\SearchCore\Connection\Elasticsearch\DocumentFactory::getDocument()`` now
throws an ``\InvalidArgumentException`` instead of ``\Exception``, if no
``search_identifier`` was provided.

* ``Codappix\SearchCore\Connection\Elasticsearch\IndexFactory::getIndex()`` now
  throws an ``\InvalidArgumentException`` if the index does not exist. Leaving
  handling up to the caller.

  Before the index was created if it didn't exist. To create an index, a new method
  ``public function createIndex(Connection $connection, string $documentType): \Elastica\Index``
  was added. This method will only create the index if it didn't exist before.
  In the end, the index is returned always. Making this method a 1:1 replacement for
  older ``getIndex()``.
