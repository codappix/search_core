.. highlight:: bash
.. _installation:

Installation
============

The extension can be installed through composer::

    composer require "leonmrni/search_core dev-master as 1.0.x-dev"

or by `downloading`_ and placing it inside the :file:`typo3conf/ext`-Folder of your installation.
In that case you need to install all dependencies yourself. Dependencies are:

.. literalinclude:: ../../composer.json
   :caption: Dependencies from composer.json
   :lines: 19-21
   :dedent: 8

Afterwards you need to enable the extension through the extension manager and include the static
TypoScript setup.

.. _downloading: https://github.com/DanielSiepmann/search_core/archive/master.zip
