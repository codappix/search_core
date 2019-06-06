.. highlight:: bash
.. _installation:

Installation
============

Composer
--------

The extension can be installed through composer::

    composer require "codappix/search_core" "^1.0"

Download
--------

You can also `download`_ the extension and placing it inside the :file:`typo3conf/ext`-Folder of
your installation.  In that case you need to install all dependencies yourself. Dependencies are:

PHP
   Higher or equal then 7.2.0 and lower then 8

TYPO3 CMS
   Higher or equal then 9.5.0 and lower then 10.0.0

Library https://elastica.io/
   In version 3.2.x

Setup
-----

Afterwards you need to enable the extension through the extension manager and include the static
TypoScript setup.

If you **don't** want to use the included Elasticsearch integration, you have to disable it in the
extension manager configuration of the extension by checking the checkbox.
It's currently enabled by default but will be moved into its own extension in the future.

.. _download: https://github.com/codappix/search_core/archive/develop.zip
