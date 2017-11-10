.. highlight:: bash
.. _usage:

Usage
=====

.. _usage_manual_indexing:

Manual indexing
---------------

You can trigger indexing from CLI::

    ./typo3/cli_dispatch.phpsh extbase index:index --identifier 'tt_content'

This will index the table ``tt_content`` using the :ref:`TcaIndexer`.

Only one index per call is available, to run multiple indexers, just make multiple calls.
The indexers have to be defined in TypoScript via :ref:`configuration_options_index`.

.. _usage_auto_indexing:

Auto indexing
-------------

Indexing is done through hooks every time an record is changed.
The tables have to be configured via :ref:`configuration_options_index`.

.. note::

  Not all hook operations are supported yet, see :issue:`27`.

.. _usage_form_finisher:

Form finisher
-------------

A form finisher is provided to integrate indexing into form extension.

Add form finisher to your available finishers and configure it like:

.. code-block:: yaml
   :linenos:

    -
        identifier: SearchCoreIndexer
        options:
            action: 'delete'
            indexIdentifier: 'fe_users'
            recordUid: '{FeUser.user.uid}'

All three options are necessary, where

``action``
    Is one of ``delete``, ``update`` or ``add``.
``indexIdentifier``
    Is a configured index identifier.
``recordUid``
    Has to be the uid of the record to index.

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

   <!-- Case sensitive for fields of type keyword. -->
   <f:form.textfield property="filter.exampleName" value="the value to match" />

.. _usage_searching_facets:

Facets
""""""

To add a facet as criteria for searching, use :ref:`usage_searching_filter`.

To display facet results use:

.. code-block:: html

    <f:for each="{searchResult.facets}" as="facet">
        <f:for each="{facet.options}" as="option">
            <label for="{option.name}-desktop">
                <f:form.checkbox value="{option.name}" property="filter.{facet.field}" />
                {f:translate(id: 'search.filter.channel.{option.name}', default: option.name, extensionName: 'SitePackage')}
                ({option.count})
            </label>
        </f:for>
    </f:for>

