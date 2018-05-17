Breaking Change 120 "Pass facets configuration to Elasticsearch"
================================================================

In order to allow arbitrary facet configuration, we do not process the facet configuration anymore.
Instead integrators are able to configure facets for search service "as is". We just pipe the
configuration through.

Therefore the following, which worked before, does not work anymore:

.. code-block:: typoscript
   :linenos:
   :emphasize-lines: 4

    plugin.tx_searchcore.settings.search {
        facets {
            category {
                field = categories
            }
        }
    }

Instead you have to provide the full configuration yourself:

.. code-block:: typoscript
   :linenos:
   :emphasize-lines: 4,6

    plugin.tx_searchcore.settings.search {
        facets {
            category {
                terms {
                    field = categories
                }
            }
        }
    }

You need to add line 4 and 6, the additional level ``terms`` for Elasticsearch.

See :issue:`120`.
