Feature 134 "Enable indexing of tt_content records of CType Header"
===================================================================

Before, only ``bodytext`` was used to generate content while indexing pages.

As there are content elements like ``header`` where this field is empty, but content is still
available, it's now possible to configure the fields.
This makes it also possible to configure further custom content elements with new columns.

A new TypoScript option is now available, and ``header`` is added by default, see
:ref:`contentFields`.

See :issue:`134`.
