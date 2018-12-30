.. _concepts:

Concepts
========

The main concept is to provide a foundation where other developers can profit from, to provide
integrations into search services like Elasticsearch, Algolia, …. But also to provide
an ETL Framework.

Our code contains the following concepts which should be understand:

.. _concepts_connections:

Connections
-----------

Different search services can provide integrations. ``search_core`` only provides abstractions and
interfaces. The main purpose is to provide a stable API between TYPO3 and concrete connection.

For information about implementing a new connection, take a look at :ref:`development_connection`.

These are equivalent to "Load" of ETL while "indexing", and equivalent to
"Extraction" in frontend mode.

.. _concepts_indexing:

Indexing
--------

Indexing is the process of collecting and preparing data, before sending it to a Connection.
The indexing is done by one of the available indexer. Indexer are identified by a key, as configured
in TypoScript.

Currently :ref:`TcaIndexer` and :ref:`PagesIndexer` are provided.

For information about implementing a new indexer, take a look at :ref:`development_indexer`.

This is the process of "loading" data inside the ETL.

.. _concepts_dataprocessing:

DataProcessing
^^^^^^^^^^^^^^

Before data is transfered to search service, it can be processed by "DataProcessors" like already
known by :ref:`t3tsref:cobj-fluidtemplate-properties-dataprocessing` of :ref:`t3tsref:cobj-fluidtemplate`.
The same is true for retrieved search results. They can be processed again by "DataProcessors" to
prepare data for display in Templates or further usage.

This should keep indexers simple and move logic to DataProcessors. This makes most parts highly
flexible as integrators are able to configure DataProcessors and change their order.

Configuration is done through TypoScript, see :ref:`dataprocessors`.

For information about implementing a new DataProcessor, take a look at :ref:`development_dataprocessor`.

This is the "transforming" step of ETL.
