``Codappix\SearchCore\DataProcessing\RemoveProcessor``
======================================================

Will remove fields from record, e.g. if you do not want to sent them to elasticsearch at all.

Possible Options:

``fields``
    Comma separated list of fields to remove from record.

Example::

    plugin.tx_searchcore.settings.indexing.tt_content.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\RemoveProcessor
        1 {
            fields = description
        }
        2 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        2 {
            fields = description, another_field
        }
    }

