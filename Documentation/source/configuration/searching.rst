.. _configuration_options_search:

Searching
=========

.. _size:

size
----

    Used by: Elasticsearch connection while building search query.

    Defined how many search results should be fetched to be available in search result.

    Example::

        plugin.tx_searchcore.settings.searching.size = 50

    Default if not configured is 10.

.. _facets:

facets
------

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

.. _filter:

``filter``
"""""""""""

    Used by: While building search request.

    Define filter that should be set for all requests.

    Example::

        plugin.tx_searchcore.settings.searching.filter {
            property = value
        }

    For Elasticsearch the fields have to be filterable, e.g. need a mapping as ``keyword``.

.. _minimumShouldMatch:

minimumShouldMatch
------------------

    Used by: Elasticsearch connection while building search query.

    Define the minimum match for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-minimum-should-match.html

    Example::

        plugin.tx_searchcore.settings.searching.minimumShouldMatch = 50%

.. _boost:

boost
-----

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

fieldValueFactor
----------------

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

.. _mode:

``mode``
""""""""

    Used by: Controller while preparing action.

    Define to switch from search to filter mode.

    Example::

        plugin.tx_searchcore.settings.searching {
            mode = filter
        }

    Only ``filter`` is allowed as value. Will submit an empty query to switch to filter mode.
