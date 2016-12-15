.. _indexer:

Indexer
=======

See Concept of :ref:`concepts_indexing` for further background information.

The extension provides the following indexer out of the box:

.. _TcaIndexer:

TcaIndexer
----------

Provides zero configuration TYPO3 integration by using the :ref:`t3-tca-ref:start`. You just can
start indexing TYPO3.

The indexer will use the TCA to fetch all necessary information like relations. Currently the
implementation is very basic. In future it will also provide mapping for Elasticsearch and further
stuff.

The indexer is configurable through the following options:

* :ref:`allowedTables`

* :ref:`rootLineBlacklist`

* :ref:`additionalWhereClause`
