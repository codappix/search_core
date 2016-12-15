.. highlight:: bash
.. _usage:

Usage
=====

.. _usage_manual_indexing:

Manual indexing
---------------

You can trigger indexing from CLI::

    ./typo3/cli_dispatch.phpsh extbase index:index --table 'tt_content'

This will index the table ``tt_content`` using the :ref:`TcaIndexer`.

.. _usage_auto_indexing:

Auto indexing
-------------

Indexing is done through hooks everytime an record is changed.
