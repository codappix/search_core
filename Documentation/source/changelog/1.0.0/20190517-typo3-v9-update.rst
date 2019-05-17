Breaking change "TYPO3 v9 LTS Update"
=====================================

The extension does now officially support TYPO3 CMS v9.5 LTS.
This change contains some breaking changes:

* Support for TYPO3 versions lower then 9.5 LTS has been dropped.

* Due to dropped TYPO3 CMS < v9 support, also all PHP Code within ``Compatibility``
  namespace was removed.

* Fluid variable ``{request.query}`` is no longer provided, due to internal API
  changes. Use ``{request.searchTerm}`` instead.

* PHP Interface ``\Codappix\SearchCore\Connection\SearchRequestInterface`` has
  changed, due to extending TYPO3 Interface
  ``\TYPO3\CMS\Extbase\Persistence\QueryResultInterface``.

  Therefore also PHP class ``\Codappix\SearchCore\Domain\Model\SearchRequest`` has
  been adjusted.
