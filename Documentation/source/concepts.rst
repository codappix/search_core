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

The indexing is done by one of the available indexer. It should be possible to define the indexer to
use for certein document types. Also it should be possible to write custom indexer to use.

Currently only the :ref:`TcaIndexer` is provided.

.. _concepts_mapper:

Mapper
------

In between are mappers. They are tightly related to a single connection. Some search services like
Elasticsearch provide a way to map certain properties to build aggregates and such features.
Therefore mapping is part of the connection, but still it need to know which mapping should be
applied for each property.

Currently only the `TcaMapper` is provided for Elasticsearch to provide mapping based on TCA.

Further mapper can easily be integrated as a single factory is used to get the corresponding mapper
based on the current type while indexing.
