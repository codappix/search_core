.. highlight:: typoscript

.. _configuration:

Configuration
=============

Installation wide configuration is handled inside of the extension manager. Just check out the
options there, they all have labels.

Everything else is configured through TypoScript. If you overwrite them through `setup` make sure to
keep them in the `module` area as they will be accessed from backend mode of TYPO3 for indexing. Do
so by placing the following line at the end::

    module.tx_searchcore < plugin.tx_searchcore

.. todo::

   We will use references inside the extension to make the above unnecessary in the future.

The structure is following TYPO3 Extbase conventions. All settings are placed inside of::

    plugin.tx_searchcore.settings

Here is the example default configuration that's provided through static include:

.. code-block:: typoscript

   plugin {
       tx_searchcore {
           settings {
               connections {
                   elasticsearch {
                       host = localhost
                       port = 9200
                       index = typo3content
                   }
               }

               indexing {
                   tt_content {
                       additionalWhereClause = tt_content.CType NOT IN ('gridelements_pi1', 'list', 'div', 'menu', 'shortcut', 'search', 'login') AND (tt_content.bodytext != '' OR tt_content.header != '')
                   }

                   pages {
                       additionalWhereClause = pages.doktype NOT IN (3, 199, 6, 254, 255)
                       abstractFields = abstract, description, bodytext
                       contentFields = header, bodytext
                   }
               }
           }
       }
   }

.. code-block:: typoscript

   plugin {
       tx_searchcore {
           settings {
               connections {
                   elasticsearch {
                       host = {$plugin.tx_searchcore.settings.connections.elasticsearch.host}
                       port = {$plugin.tx_searchcore.settings.connections.elasticsearch.port}
                       index = {$plugin.tx_searchcore.settings.connections.elasticsearch.index}
                   }
               }

               indexing {
                   # Not for direct indexing therefore no indexer.
                   # Used to configure tt_content fetching while indexing pages
                   tt_content {
                       additionalWhereClause = {$plugin.tx_searchcore.settings.indexing.tt_content.additionalWhereClause}
                   }

                   pages {
                       indexer = Codappix\SearchCore\Domain\Index\TcaIndexer\PagesIndexer
                       additionalWhereClause = {$plugin.tx_searchcore.settings.indexing.pages.additionalWhereClause}
                       abstractFields = {$plugin.tx_searchcore.settings.indexing.pages.abstractFields}
                       contentFields = {$plugin.tx_searchcore.settings.indexing.pages.contentFields}
                   }
               }

               searching {
                   fields {
                       query = _all
                   }
               }
           }
       }
   }

   module.tx_searchcore < plugin.tx_searchcore

.. _configuration_options:

Options
-------

The following sections contains the different options grouped by their applied area, e.g. for
:ref:`connections` and :ref:`indexer`: ``plugin.tx_searchcore.settings.connection`` or
``plugin.tx_searchcore.settings.indexing``:

.. toctree::
   :maxdepth: 1
   :glob:

   configuration/connections
   configuration/indexing
   configuration/searching
