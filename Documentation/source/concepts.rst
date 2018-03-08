.. _concepts:

Concepts
========

The extension is built with the following concepts in mind.

.. _concepts_connections:

Connections
-----------

It should be possible to use different search services like elasticsearch and solr out of the box.
If a service is not contained, it should be possible to implement the necessary part by implementing
the necessary interfaces and configuring the extension to use the new connection.

Also it should be possible to use multiple connections at once. This way multiple search services
can be used in the same installation.

Currently only :ref:`Elasticsearch` is provided.

.. _concepts_indexing:

Indexing
--------

The indexing is done by one of the available indexer. For each identifier it's possible to define
the indexer to use. Also it's possible to write custom indexer to use.

Currently only the :ref:`TcaIndexer` is provided.

.. _concepts_indexing_dataprocessing:

DataProcessing
^^^^^^^^^^^^^^

Before data is transfered to search service, it can be processed by "DataProcessors" like already
known by :ref:`t3tsref:cobj-fluidtemplate-properties-dataprocessing` of :ref:`t3tsref:cobj-fluidtemplate`.
The same is true for retrieved search results. They can be processed again by "DataProcessors" to
prepare data for display in Templates or further usage.

Configuration is done through TypoScript, see :ref:`dataProcessing`.
