``Codappix\SearchCore\DataProcessing\RemoveProcessor``
======================================================

Will remove fields from record.

Possible Options:

``fields``
    Comma separated list of fields to remove from record.

Example::

    plugin.tx_searchcore.settings.indexing.tt_content.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\RemoveProcessor
        1 {
            fields = description
        }
        2 = Codappix\SearchCore\DataProcessing\RemoveProcessor
        2 {
            fields = description, another_field
        }
    }

