.. _connections:

Connections
===========

See Concept of :ref:`concepts_connections` for further background information.

The extension provides the following connections out of the box:

.. _Elasticsearch:

Elasticsearch
-------------

Integrates `Elasticsearch`_ using `elastica`_ into TYPO3.

Provides basic support like indexing with mappings, facets, full text search at the moment.

The connection is configurable through the following options:

* :ref:`host`

* :ref:`port`

The connection also provides basic mapping capabilities based on TCA fields indicated as *boolean*
and *date* are converted and mapped while indexing, independent of indexer at the moment.
If further indexer are provided there need to be a mechanism in the future.

Mapping is done using the TCA at the moment, see :ref:`concepts_mapper`.

.. todo:: 

    * Document facet configuration

.. _elastic Elasticsearch: https://www.elastic.co/products/elasticsearch
.. _elastica: http://elastica.io/
