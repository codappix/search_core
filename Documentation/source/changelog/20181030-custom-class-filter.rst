Feature "Manipulate search filter"
==================================

You can manipulate the filter via a custom class through the ``custom`` type typoscript
mapping.::

  plugin.tx_searchcore.settings.searching {
    mapping {
      filter {
        frontendUserAccess {
          type = custom
          custom = Codappix\SearchCore\Domain\Search\Filter\FrontendUserAccessFilter
        }
      }
    }
  }

If you want to force this filter on searching make sure to define them as default filters like:::

  plugin.tx_searchcore.settings.searching {
    filter {
      frontendUserAccess = search_access
    }
  }

Example
-------
See ``Codappix\SearchCore\Domain\Search\Filter\FrontendUserAccessFilter`` as example.

