``Codappix\SearchCore\DataProcessing\ContentObjectDataProcessorAdapterProcessor``
=================================================================================

Will execute an existing TYPO3 data processor.

Possible Options:

``_dataProcessor``
    Necessary, defined which data processor to apply. Provide the same as you would to call the
    processor.
``_table``
    Defines the "current" table as used by some processors, e.g.
    ``TYPO3\CMS\Frontend\DataProcessing\FilesProcessor``.

All further options are passed to the configured data processor. Therefore they are documented at
each data processor.

Example::

    plugin.tx_searchcore.settings.searching.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\ContentObjectDataProcessorAdapterProcessor
        1 {
            _table = pages
            _dataProcessor = TYPO3\CMS\Frontend\DataProcessing\FilesProcessor

            references.fieldName = media
            as = images
        }
    }

The above example will create a new field ``images`` with resolved FAL relations from ``media``
field.
