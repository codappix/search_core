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

.. _spellcheck:

spellcheck
----------

    Used by: Elasticsearch connection while building search query.

    Configure a suggest to return spell checking suggestions, e.g. to be displayed in template.

    Example::

        plugin.tx_searchcore.settings.searching.spellcheck {
            field = search_spellcheck
            size = 10
        }

    The above example configured to use the field ``search_spellcheck`` to be used to provide spell
    checking suggestions. Also a maximum of ``10`` suggestions are fetched, default is ``5``.

    The field to use has to be configured, e.g. through :ref:`dataProcessing`. The value of spell
    checking suggestion depends highly on indexed data and configured :ref:`mapping` and
    :ref:`index`.

    A fully working example::

        plugin.tx_searchcore.settings {
            pages {
                indexer = Codappix\SearchCore\Domain\Index\TcaIndexer
                index {
                    analysis {
                        analyzer {
                            spellcheck {
                                type = custom
                                tokenizer = lowercase
                                filter = minimumLength
                            }
                        }
                        filter {
                            minimumLength {
                                type = length
                                min = 4
                            }
                        }
                    }
                }

                mapping {
                    search_spellcheck {
                        type = text
                        analyzer = spellcheck
                    }
                }

                dataProcessing {
                    1 = Codappix\SearchCore\DataProcessing\CopyToProcessor
                    1 {
                        to = search_spellcheck
                    }
                }
            }

            searching {
                spellcheck {
                    field = search_spellcheck
                    size = 10
                }
            }
        }

    Also take a look at
    https://www.elastic.co/guide/en/elasticsearch/guide/current/fuzzy-matching.html .
