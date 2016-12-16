.. _indexer:

Indexer
=======

See Concept of :ref:`concepts_indexing` for further background information.

The extension provides the following indexer out of the box:

.. _TcaIndexer:

TcaIndexer
----------

Provides zero configuration TYPO3 integration by using the :ref:`t3TcaRef:start`. You just can
start indexing TYPO3.

The indexer will use the TCA to fetch all necessary information like relations. Currently the
implementation is very basic. In future it will also provide mapping for :ref:`Elasticsearch` and
further stuff.

The indexer is configurable through the following options:

* :ref:`allowedTables`

* :ref:`rootLineBlacklist`

* :ref:`additionalWhereClause`

.. note::

  Not all relations are resolved yet, see :issue:`17` and :pr:`20`.
  Also the `pages`-Table is not available yet, see :issue:`24`.
