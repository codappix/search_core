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

Only one table per call is available, to index multiple tables just make multiple calls.
The tables have to be white listed through :ref:`allowedTables` option.

.. _usage_auto_indexing:

Auto indexing
-------------

Indexing is done through hooks every time an record is changed.
The tables have to be white listed through :ref:`allowedTables` option.

.. note::

  Not all hook operations are supported yet, see :issue:`27`.

.. _usage_searching:

Searching / Frontend Plugin
---------------------------

To provide a search interface you can insert the frontend Plugin as normal content element of type
plugin. The plugin is named *Search Core*.
