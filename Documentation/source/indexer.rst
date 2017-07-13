.. _indexer:

Indexer
=======

See Concept of :ref:`concepts_indexing` for further background information.

The extension provides the following indexer out of the box:

.. _TcaIndexer:

TcaIndexer
----------

Provides zero configuration TYPO3 integration by using the :ref:`t3tcaref:start`. You just can
start indexing TYPO3.

The indexer will use the TCA to fetch all necessary information like relations. Currently the
implementation is very basic. In future it will also provide mapping for :ref:`Elasticsearch` and
further stuff.

The indexer is configurable through the following options:

* :ref:`allowedTables`

* :ref:`rootLineBlacklist`

* :ref:`additionalWhereClause`

.. _PagesIndexer:

PagesIndexer
------------

Provides zero configuration TYPO3 integration by using the :ref:`t3tcaref:start`. You just can
start indexing TYPO3.

The indexer will use the TCA to fetch all necessary information like relations. Currently the
implementation is very basic. In future it will also provide mapping for :ref:`Elasticsearch` and
further stuff. Also all static content from each page will be concatenated into a single field to
improve search.

The indexer is configurable through the following options:

* :ref:`allowedTables`

* :ref:`rootLineBlacklist`

* :ref:`additionalWhereClause`

* :ref:`abstractFields`

.. note::

  Not all relations are resolved yet, see :issue:`17` and :pr:`20`.
