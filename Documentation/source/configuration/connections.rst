.. _configuration_options_connection:

Connections
===========

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
--------

Used by: :ref:`Elasticsearch`.

The host, e.g. ``localhost`` or an IP where the search service is reachable from TYPO3
installation.

Example::

    plugin.tx_searchcore.settings.connections.elasticsearch.host = localhost

.. _port:

``port``
--------

Used by: :ref:`Elasticsearch`.

The port where search service is reachable. E.g. default ``9200`` for Elasticsearch.

Example::

    plugin.tx_searchcore.settings.connections.elasticsearch.port = 9200

.. _index:

``index``
--------

Used by: :ref:`Elasticsearch`.

The index where the documents are being indexed to. E.g. default ``typo3content`` for Elasticsearch.

Example::

    plugin.tx_searchcore.settings.connections.elasticsearch.index = typo3content



