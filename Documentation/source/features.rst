.. _features:

Features
========

The following features are currently provided:

.. _features_indexing:

Indexing
--------

Indexing data to Elasticsearch is provided. The extension delivers an indexer for TCA with zero
configuration needs. Still it's possible to configure the indexer.

Own indexer are not possible yet, but will.

.. _features_search:

Searching
---------

Currently all fields are searched for a single search input.

Also multiple filter are supported. Filtering results by fields for string contents.

.. _features_planned:

Planned
---------

The following features are currently planned and will be integrated:

#. Mapping Configuration
   Allowing to configure the whole mapping, to define type of input, e.g. integer, keyword.


#. Facets / Aggregates
   Based on the mapping configuration, facets will be configurable and fetched. Therefore mapping is
   required and we will adjust the result set to be of a custom model providing all information in a
   more clean way.
