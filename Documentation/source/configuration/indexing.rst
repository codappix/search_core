.. _configuration_options_index:

Indexing
========

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

rootLineBlacklist
-----------------

Used by: :ref:`TcaIndexer`, :ref:`PagesIndexer`.

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

additionalWhereClause
---------------------

Used by: :ref:`TcaIndexer`, :ref:`PagesIndexer`.

Add additional SQL to where clauses to determine indexable records from the table. This way you
can exclude specific records like ``tt_content`` records with specific ``CType`` values or
something else. E.g. you can add a new field to the table to exclude records from indexing.

Example::

    plugin.tx_searchcore.settings.indexing.<identifier>.additionalWhereClause = tt_content.CType NOT IN ('gridelements_pi1', 'list', 'div', 'menu')

.. attention::

    Make sure to prefix all fields with the corresponding table name. The selection from
    database will contain joins and can lead to SQL errors if a field exists in multiple tables.

.. _abstractFields:

abstractFields
--------------

Used by: :ref:`PagesIndexer`.

Define which field should be used to provide the auto generated field "search_abstract".
The fields have to exist in the record to be indexed. Therefore fields like ``content`` are also
possible.

Example::

    # As last fallback we use the content of the page
    plugin.tx_searchcore.settings.indexing.<identifier>.abstractFields := addToList(content)

Default::

    abstract, description, bodytext

.. _mapping:

mapping
-------

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

index
-----

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

.. _dataProcessing:

dataProcessing
--------------

Used by: All connections while indexing, due to implementation inside ``AbstractIndexer``.

Configure modifications on each document before sending it to the configured connection. Same as
provided by TYPO3 for :ref:`t3tsref:cobj-fluidtemplate` through
:ref:`t3tsref:cobj-fluidtemplate-properties-dataprocessing`.

All processors are applied in configured order. Allowing to work with already processed data.

Example::

    plugin.tx_searchcore.settings.indexing.tt_content.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        1 {
            to = search_spellcheck
        }

        2 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        2 {
            to = search_all
        }
    }

The above example will copy all existing fields to the field ``search_spellcheck``. Afterwards
all fields, including ``search_spellcheck`` will be copied to ``search_all``.

.. include:: /configuration/dataProcessing/availableAndPlanned.rst

Also data processors are available for search results too, see :ref:`searching_dataProcessing`.
