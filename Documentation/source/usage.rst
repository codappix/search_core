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

Please provide your own template, the extension will not deliver a useful template for now.

The extbase mapping is used, this way you can create a form:

.. code-block:: html

   <f:form name="searchRequest" object="{searchRequest}">
       <f:form.textfield property="query" />
       <f:form.submit value="search" />
   </f:form>

.. _usage_searching_filter:

Filter
""""""

Thanks to extbase mapping, filter are added to the form:

.. code-block:: html
   :emphasize-lines: 3

   <f:form name="searchRequest" object="{searchRequest}">
       <f:form.textfield property="query" />
       <f:form.textfield property="filter.exampleName" value="the value to match" />
       <f:form.submit value="search" />
   </f:form>
