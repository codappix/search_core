.. _dataprocessors:

DataProcessors
==============

See Concept of :ref:`concepts_dataprocessing` for further background information.

For information about implementing a new DataProcessor, take a look at
:ref:`development_dataprocessor`.

Same as provided by TYPO3 for :ref:`t3tsref:cobj-fluidtemplate` through
:ref:`t3tsref:cobj-fluidtemplate-properties-dataprocessing`.

.. _dataprocessors_usage:

Usage
-----

All processors are applied in configured order. Allowing to work with already processed data.
They can be applied during indexing and for search results.

Example for indexing::

    plugin.tx_searchcore.settings.indexing.pages.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        1 {
            to = search_spellcheck
        }

        2 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        2 {
            to = search_all
        }
    }

The above example will copy all existing fields to the field ``search_spellcheck``. Afterwards
all fields, including ``search_spellcheck`` will be copied to ``search_all``.

Example for search results::

    plugin.tx_searchcore.settings.searching.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        1 {
            to = search_spellcheck
        }

        2 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        2 {
            to = search_all
        }
    }

The above example will copy all existing fields to the field ``search_spellcheck``. Afterwards
all fields, including ``search_spellcheck`` will be copied to ``search_all``.

.. _dataprocessors_availableDataProcessors:

Available DataProcessors
------------------------

.. toctree::
    :maxdepth: 1
    :glob:

    /configuration/dataProcessing/ContentObjectDataProcessorAdapterProcessor
    /configuration/dataProcessing/CopyToProcessor
    /configuration/dataProcessing/GeoPointProcessor
    /configuration/dataProcessing/RemoveProcessor

.. _dataprocessors_plannedDataProcessors:

Planned DataProcessors
----------------------

    ``Codappix\SearchCore\DataProcessing\ReplaceProcessor``
        Will execute a search and replace on configured fields.

    ``Codappix\SearchCore\DataProcessing\RootLevelProcessor``
        Will attach the root level to the record.

    ``Codappix\SearchCore\DataProcessing\ChannelProcessor``
        Will add a configurable channel to the record, e.g. if you have different areas in your
        website like "products" and "infos".

    ``Codappix\SearchCore\DataProcessing\RelationResolverProcessor``
        Resolves all relations using the TCA.
        This is currently done through indexer.

.. Of course you are able to provide further processors. Just implement
.. ``Codappix\SearchCore\DataProcessing\ProcessorInterface`` and use the FQCN (=Fully qualified
.. class name) as done in the examples above.

.. By implementing also the same interface as necessary for TYPO3
.. :ref:`t3tsref:cobj-fluidtemplate-properties-dataprocessing`, you are able to reuse the same code
.. also for Fluid to prepare the same record fetched from DB for your fluid.

.. Dependency injection is possible inside of processors, as we instantiate through extbase
.. ``ObjectManager``.
