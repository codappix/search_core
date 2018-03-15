.. _configuration_options_connection:

Connections
===========

Holds settings regarding the different possible connections for search services like Elasticsearch
or Algolia.

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

The following settings are available.

.. _host:

``host``
--------

The host, e.g. ``localhost`` or an IP where the search service is reachable from TYPO3
installation.

Example::

    plugin.tx_searchcore.settings.connections.elasticsearch.host = localhost

.. _port:

``port``
--------

The port where search service is reachable. E.g. default ``9200`` for Elasticsearch.

Example::

    plugin.tx_searchcore.settings.connections.elasticsearch.port = 9200
