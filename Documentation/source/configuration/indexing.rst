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

    plugin.tx_searchcore.settings.indexing.pages.rootLineBlacklist = 3, 10, 100

.. _additionalWhereClause:

additionalWhereClause
---------------------

Used by: :ref:`TcaIndexer`, :ref:`PagesIndexer`.

Add additional SQL to where clauses to determine indexable records from the table. This way you
can exclude specific records like ``tt_content`` records with specific ``CType`` values or
something else.

Example::

    plugin.tx_searchcore.settings.indexing.tt_content.additionalWhereClause = tt_content.CType NOT IN ('gridelements_pi1', 'list', 'div', 'menu')

.. attention::

    Make sure to prefix all fields with the corresponding table name. The selection from
    database might contain joins and can lead to SQL errors if a field exists in multiple tables.

.. _abstractFields:

abstractFields
--------------

Used by: :ref:`PagesIndexer`.

.. note::

   Will be migrated to :ref:`dataprocessors` in the future.

Define which field should be used to provide the auto generated field "search_abstract".
The fields have to exist in the record to be indexed. Therefore fields like ``content`` are also
possible.

Example::

    # As last fallback we use the content of the page
    plugin.tx_searchcore.settings.indexing.pages.abstractFields := addToList(content)

Default::

    abstract, description, bodytext

.. _mapping:

mapping
-------

Used by: :ref:`connection_elasticsearch` connection while indexing.

Define mapping for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/mapping.html
You are able to define the mapping for each property / column.

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

Used by: :ref:`connection_elasticsearch` connection while indexing.

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

.. _indexing_dataProcessing:

dataProcessing
--------------

Used by: All connections while indexing, due to implementation inside ``AbstractIndexer``.

Configure modifications on each document before sending it to the configured connection.
For full documentation check out :ref:`dataprocessors`.
