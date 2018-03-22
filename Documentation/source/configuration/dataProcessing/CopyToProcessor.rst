``Codappix\SearchCore\DataProcessing\CopyToProcessor``
======================================================

Will copy contents of fields to other fields.

Possible Options:

``to``
    Defines the field to copy the values into. All values not false will be copied at the moment.

``from``
    Optional, defines the field to copy, can only be one field.
    If empty, all existing fields will be copied.

Example::

    plugin.tx_searchcore.settings.indexing.tt_content.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        1 {
            to = all
        }
        2 = Codappix\SearchCore\DataProcessing\CopyToProcessor
        2 {
            from = uid
            to = backup_uid
        }
    }

