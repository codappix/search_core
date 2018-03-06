The following Processor are available:

.. toctree::
    :maxdepth: 1
    :glob:

    /configuration/dataProcessing/ContentObjectDataProcessorAdapterProcessor
    /configuration/dataProcessing/CopyToProcessor
    /configuration/dataProcessing/GeoPointProcessor
    /configuration/dataProcessing/RemoveProcessor

The following Processor are planned:

    ``Codappix\SearchCore\DataProcessing\ReplaceProcessor``
        Will execute a search and replace on configured fields.

    ``Codappix\SearchCore\DataProcessing\RootLevelProcessor``
        Will attach the root level to the record.

    ``Codappix\SearchCore\DataProcessing\ChannelProcessor``
        Will add a configurable channel to the record, e.g. if you have different areas in your
        website like "products" and "infos".

    ``Codappix\SearchCore\DataProcessing\RelationResolverProcessor``
        Resolves all relations using the TCA.

Of course you are able to provide further processors. Just implement
``Codappix\SearchCore\DataProcessing\ProcessorInterface`` and use the FQCN (=Fully qualified
class name) as done in the examples above.

By implementing also the same interface as necessary for TYPO3
:ref:`t3tsref:cobj-fluidtemplate-properties-dataprocessing`, you are able to reuse the same code
also for Fluid to prepare the same record fetched from DB for your fluid.

Dependency injection is possible inside of processors, as we instantiate through extbase
``ObjectManager``.
