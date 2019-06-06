``Codappix\SearchCore\DataProcessing\TcaRelationResolvingProcessor``
====================================================================

Will resolve relations through TCA for record.
The result will be the same as in list view of TYPO3 Backend. E.g. Check boxes will be
resolved to their label, dates will be resolved to human readable representation and
relations will be resolved to their configured labels.

Combine with CopyToProcessor or exclude certain fields to keep original value for
further processing.

Mandatory Options:

``_table``
    The TCA table as found on top level of ``$GLOBALS['TCA']``.

    This will auto filled for indexing through the provided indexers. Still you can
    apply processors on results, where no information about the table exists anymore.

Possible Options:

``excludeFields``
    Comma separated list of fields to not resolve relations for.

Example::

    plugin.tx_searchcore.settings.indexing.tt_content.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\TcaRelationResolvingProcessor
        1 {
            _table = tt_content
            excludeFields = starttime, endtime
        }
    }

