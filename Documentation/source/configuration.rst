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

.. literalinclude:: ../../Configuration/TypoScript/constants.typoscript
   :language: typoscript
   :caption: Static TypoScript Constants

.. literalinclude:: ../../Configuration/TypoScript/setup.typoscript
   :language: typoscript
   :caption: Static TypoScript Setup

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
