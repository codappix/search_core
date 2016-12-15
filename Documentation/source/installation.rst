.. heighlight:: typoscript
.. _installation:

Installation
============

The extension can be installed through composer:

.. code-block:: bash

    composer require "danielsiepmann/search_core" "dev-feature/integrate-elasticsearch"

or by downloading and placing it inside the :file:`typo3conf/ext`-Folder of your installation.

Afterwards you need to enable the extension through the extension manager and include the static
typoscript setup.

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
