.. _features:

Features
========

The following features are currently provided:

.. _features_indexing:

Indexing
--------

Indexing data to Elasticsearch is provided. The extension delivers an indexer for TCA with zero
configuration needs. Still it's possible to configure the indexer.

Also custom classes can be used as indexers.

Furthermore a finisher for TYPO3 Form-Extension is provided to integrate indexing.

.. _features_search:

Searching
---------

Currently all fields are searched for a single search input.

Also multiple filter are supported. Filtering results by fields for string contents.

Facets / aggregates are also possible. Therefore a mapping has to be defined in TypoScript for
indexing, and the facets itself while searching.

.. _features_dataProcessing:

DataProcessing
==============

DataProcessing, as known from ``FLUIDTEMPLATE`` is available while indexing and for search results.
Each item can be processed by multiple processor to prepare data for indexing and output.

See :ref:`concepts_indexing_dataprocessing` in :ref:`concepts` section.

.. _features_planned:

Planned
---------

The following features are currently planned and will be integrated:

#. Pagination
   Add a pagination to search results, to allow users to walk through all results.
