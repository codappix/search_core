.. highlight:: bash
.. _installation:

Installation
============

Composer
--------

The extension can be installed through composer::

    composer require "codappix/search_core" "~0.0.8"

Note that you have to allow unstable packages:

.. code-block:: json

   {
      "minimum-stability": "dev",
      "prefer-stable": true
   }

Download
--------

You can also `download`_ the extension and placing it inside the :file:`typo3conf/ext`-Folder of
your installation.  In that case you need to install all dependencies yourself. Dependencies are:

.. literalinclude:: ../../composer.json
   :caption: Dependencies from composer.json
   :lines: 19-21
   :dedent: 8

Setup
-----

Afterwards you need to enable the extension through the extension manager and include the static
TypoScript setup.

If you **don't** want to use the included Elasticsearch integration, you have to disable it in the
extension manager configuration of the extension by checking the checkbox.
It's currently enabled by default but will be moved into its own extension in the future.

.. _download: https://github.com/codappix/search_core/archive/develop.zip
