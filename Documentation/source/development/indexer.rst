.. _development_indexer:

Develop a new Indexer
=====================

Make sure you understood :ref:`concepts_indexing`.

Each indexer has to be a single class which implements
``Codappix\SearchCore\Domain\Index\IndexerInterface``.

The indexer should call the connection with all necessary information about the document(s) to
trigger indexing or deletion of whole index.

As this is the "indexer", deletion of single documents is directly processed by the connection.

``setIdentifier`` is called with the identifier of the current Indexer. This might be usefull to
fetch configuration, related to the indexing, from
``Codappix\SearchCore\Configuration\ConfigurationContainerInterface``.

Dependency Injection is working for custom indexers, therefore you are able to inject the
``ConfigurationContainerInterface``.
