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

Currently no constants are available, but this will change in the near future to make configuration
easier.

The strucutre is following TYPO3 Extbase conventions. All settings are placed inside of::

    plugin.tx_searchcore.settings

Here is the example default configuration that's provided through static setup:

.. literalinclude:: ../../Configuration/TypoScript/setup.txt
   :language: typoscript
   :linenos:
   :caption: Static TypoScript Setup

.. _configuration_options:

Options
-------

The following section contain the different options for e.g.
``plugin.tx_searchcore.settings.connection`` or ``plugin.tx_searchcore.settings.index``.

.. _configuration_options_connection:

connection
^^^^^^^^^^

Holds settings regarding the connection to search service like elasticsearch or solr.

Configured as::

    plugin {
        tx_searchcore {
            settings {
                connection {
                    // the settings
                }
            }
        }
    }

.. _host:

``host``
""""""""

    Used by: :ref:`Elasticsearch`.

    The host, e.g. ``localhost`` or an ip where the search service is reachable from TYPO3
    installation.

    Example::

        plugin.tx_searchcore.settings.connection.host = localhost

.. _port:

``port``
""""""""

    Used by: :ref:`Elasticsearch`.

    The port where search service is reachable. E.g. default ``9200`` for elasticsearch.

    Example::

        plugin.tx_searchcore.settings.connection.port = 9200



.. _configuration_options_index:

index
^^^^^

Holds settings regarding the indexing of TYPO3 records to search service.

Configured as::

    plugin {
        tx_searchcore {
            settings {
                index {
                    // the settings
                }
            }
        }
    }

.. _allowedTables:

``allowedTables``
"""""""""""""""""

    Used by: :ref:`TcaIndexer`.

    Defines which TYPO3 tables are allowed to be indexed. Only whitelisted tables will be processed
    through Command Line Interface and Hooks.

    Contains a comma separated list of table names. Spaces are trimmed.

    Example::

        plugin.tx_searchcore.settings.index.allowedTables = tt_content, fe_users

.. _rootLineBlacklist:

``rootLineBlacklist``
"""""""""""""""""""""

    Used by: :ref:`TcaIndexer`.

    Defines a blacklist of page uids. Records below any of these pages, or subpages, are not
    indexed. This allows you to define areas that should not be indexed.
    The page attribute *No Search* is also taken into account to prevent indexing records from only one
    page without recursion.

    Contains a comma separated list of table names. Spaces are trimmed.

    Example::

        plugin.tx_searchcore.settings.index.rootLineBlacklist = 3, 10, 100

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

        plugin.tx_searchcore.settings.index.tt_content.additionalWhereClause = tt_content.CType NOT IN ('gridelements_pi1', 'list', 'div', 'menu')

    .. attention::

        Make sure to prefix all fields with the corresponding table name. The selection from
        database will contain joins and can lead to SQL errors if a field exists in multiple tables.
