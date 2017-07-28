.. highlight:: typoscript

.. _configuration:

Configuration
=============

The extension offers the following configuration options through TypoScript. If you overwrite them
through `setup` make sure to keep them in the `module` area as they will be accessed from backend
mode of TYPO3. Do so by placing the following line at the end::

    module.tx_searchcore < plugin.tx_searchcore

.. todo::

   We will use references inside the extension to make the above unnecessary in the future.

The structure is following TYPO3 Extbase conventions. All settings are placed inside of::

    plugin.tx_searchcore.settings

Here is the example default configuration that's provided through static include:

.. literalinclude:: ../../Configuration/TypoScript/constants.txt
   :language: typoscript
   :linenos:
   :caption: Static TypoScript Constants

.. literalinclude:: ../../Configuration/TypoScript/setup.txt
   :language: typoscript
   :linenos:
   :caption: Static TypoScript Setup

.. _configuration_options:

Options
-------

The following section contains the different options, e.g. for :ref:`connections` and
:ref:`indexer`: ``plugin.tx_searchcore.settings.connection`` or
``plugin.tx_searchcore.settings.indexing``.

.. _configuration_options_connection:

connections
^^^^^^^^^^^

Holds settings regarding the different possible connections for search services like Elasticsearch
or Solr.

Configured as::

    plugin {
        tx_searchcore {
            settings {
                connections {
                    connectionName {
                        // the settings
                    }
                }
            }
        }
    }

Where ``connectionName`` is one of the available :ref:`connections`.

The following settings are available. For each setting its documented which connection consumes it.

.. _host:

``host``
""""""""

    Used by: :ref:`Elasticsearch`.

    The host, e.g. ``localhost`` or an IP where the search service is reachable from TYPO3
    installation.

    Example::

        plugin.tx_searchcore.settings.connections.elasticsearch.host = localhost

.. _port:

``port``
""""""""

    Used by: :ref:`Elasticsearch`.

    The port where search service is reachable. E.g. default ``9200`` for Elasticsearch.

    Example::

        plugin.tx_searchcore.settings.connections.elasticsearch.port = 9200


.. _configuration_options_index:

Indexing
^^^^^^^^

Holds settings regarding the indexing, e.g. of TYPO3 records, to search services.

Configured as::

    plugin {
        tx_searchcore {
            settings {
                indexing {
                    identifier {
                        indexer = FullyQualifiedClassname
                        // the settings
                    }
                }
            }
        }
    }

Where ``identifier`` is up to you, but should match table names to make :ref:`TcaIndexer` work.

The following settings are available. For each setting its documented which indexer consumes it.

.. _rootLineBlacklist:

``rootLineBlacklist``
"""""""""""""""""""""

    Used by: :ref:`TcaIndexer`.

    Defines a blacklist of page uids. Records below any of these pages, or subpages, are not
    indexed. This allows you to define areas that should not be indexed.
    The page attribute *No Search* is also taken into account to prevent indexing records from only one
    page without recursion.

    Contains a comma separated list of page uids. Spaces are trimmed.

    Example::

        plugin.tx_searchcore.settings.indexing.<identifier>.rootLineBlacklist = 3, 10, 100

Also it's possible to define some behaviour for the different document types. In context of TYPO3
tables are used as document types 1:1. It's possible to configure different tables. The following
options are available:

.. _additionalWhereClause:

``additionalWhereClause``
"""""""""""""""""""""""""

    Used by: :ref:`TcaIndexer`.

    Add additional SQL to where clauses to determine indexable records from the table. This way you
    can exclude specific records like ``tt_content`` records with specific ``CType`` values or
    something else. E.g. you can add a new field to the table to exclude records from indexing.

    Example::

        plugin.tx_searchcore.settings.indexing.<identifier>.additionalWhereClause = tt_content.CType NOT IN ('gridelements_pi1', 'list', 'div', 'menu')

    .. attention::

        Make sure to prefix all fields with the corresponding table name. The selection from
        database will contain joins and can lead to SQL errors if a field exists in multiple tables.

.. _mapping:

``mapping``
"""""""""""

    Used by: Elasticsearch connection while indexing.

    Define mapping for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/mapping.html
    You are able to define the mapping for each property / columns.

    Example::

        plugin.tx_searchcore.settings.indexing.tt_content.mapping {
            CType {
                type = keyword
            }
        }

    The above example will define the ``CType`` field of ``tt_content`` as ``type: keyword``. This
    makes building a facet possible.


.. _index:

``index``
"""""""""

    Used by: Elasticsearch connection while indexing.

    Define index for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/indices-create-index.html

    Example::

        plugin.tx_searchcore.settings.indexing.tt_content.index {
            analysis {
                analyzer {
                    ngram4 {
                        type = custom
                        tokenizer = ngram4
                        char_filter = html_strip
                        filter = lowercase, asciifolding
                    }
                }

                tokenizer {
                    ngram4 {
                        type = ngram
                        min_gram = 4
                        max_gram = 4
                    }
                }
            }
        }

    ``char_filter`` and ``filter`` are a comma separated list of options.

.. _configuration_options_search:

Searching
^^^^^^^^^

.. _size:

``size``
""""""""

    Used by: Elasticsearch connection while building search query.

    Defined how many search results should be fetched to be available in search result.

    Example::

        plugin.tx_searchcore.settings.searching.size = 50

    Default if not configured is 10.

.. _facets:

``facets``
"""""""""""

    Used by: Elasticsearch connection while building search query.

    Define aggregations for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/search-aggregations-bucket-terms-aggregation.html
    Currently only the term facet is provided.

    Example::

        plugin.tx_searchcore.settings.searching.facets {
            contentTypes {
                field = CType
            }
        }

    The above example will provide a facet with options for all found ``CType`` results together
    with a count.

.. _minimumShouldMatch:

``minimumShouldMatch``
""""""""""""""""""""""

    Used by: Elasticsearch connection while building search query.

    Define the minimum match for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-minimum-should-match.html

    Example::

        plugin.tx_searchcore.settings.searching.minimumShouldMatch = 50%

.. _boost:

``boost``
"""""""""

    Used by: Elasticsearch connection while building search query.

    Define fields that should boost the score for results.

    Example::

        plugin.tx_searchcore.settings.searching.boost {
            search_title = 3
            search_abstract = 1.5
        }

    For further information take a look at
    https://www.elastic.co/guide/en/elasticsearch/guide/2.x/_boosting_query_clauses.html

.. _fieldValueFactor:

``fieldValueFactor``
""""""""""""""""""""

    Used by: Elasticsearch connection while building search query.

    Define a field to use as a factor for scoring. The configuration is passed through to elastic
    search ``field_value_factor``, see: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-function-score-query.html#function-field-value-factor

    Example::

        plugin.tx_searchcore.settings.searching.field_value_factor {
            field = rootlineLevel
            modifier = reciprocal
            factor = 2
            missing = 1
        }
