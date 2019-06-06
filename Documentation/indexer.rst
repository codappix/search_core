.. _indexer:

Indexer
=======

See Concept of :ref:`concepts_indexing` for further background information.

For information about implementing a new indexer, take a look at :ref:`development_indexer`.

The extension provides the following indexer out of the box:

.. _TcaIndexer:

TcaIndexer
----------

Provides zero configuration TYPO3 integration by using the :ref:`t3tcaref:start`. You just can
start indexing TYPO3.

Just add the indexer for a TYPO3 table. The indexer will use the TCA to fetch all necessary
information like relations.

.. _PagesIndexer:

PagesIndexer
------------

Provides zero configuration TYPO3 integration by using the :ref:`t3tcaref:start`. You just can
start indexing TYPO3.

The indexer will use the TCA to fetch all necessary information like relations. Currently the
implementation is very basic.
