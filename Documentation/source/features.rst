.. _features:

Features
========

The following features are available:

.. _features_indexing:

Indexing
--------

Indexing of data is possible. We deliver an indexer for TCA with zero configuration needs. You can
also provide custom indexer for arbitrary data which is not indexable through TCA.

Also a finisher for TYPO3 Form-Extension is provided to integrate indexing after something was
update through the Form-Extension.

Indexing is done through Hooks and CLI. We therefore provide commands to index and delete indexed
data.

.. _features_search:

Searching
---------

.. note::
    Currently only integrated for Elasticsearch with no abstraction.
    If you need to implement your own search, please open an issue on Github and we will change the code
    base.

Via TypoScript it's possible to configure the fields to query, minimum match and script fields.
Also multiple filter are supported, filtering results by fields.

Facets / aggregates are also possible. Therefore a mapping has to be defined in TypoScript for
indexing, and the facets itself while searching.

.. _features_dataProcessing:

DataProcessing
--------------

DataProcessing, as known from ``FLUIDTEMPLATE``, is available while indexing and for search results.
Each record and result item can be processed by multiple processor to prepare data for indexing and
output.

See :ref:`concepts_dataprocessing` in :ref:`concepts` section.

.. _features_planned:

Planned
-------

The following features are currently planned and will be integrated:

#. :issue:`25` Multi language.
#. :issue:`94` Respect access rights while indexing relations.
#. :issue:`75` Configuration of index name (for Elasticsearch).

For a full list, check out our `open issues`_.

.. _open issues: https://github.com/Codappix/search_core/issues
