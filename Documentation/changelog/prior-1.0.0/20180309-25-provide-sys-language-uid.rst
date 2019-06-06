.. _change-feature-25:

Feature 25 "Respect multiple languages" - Provide sys_language_uid
==================================================================

Previously we did not fetch ``sys_language_uid`` field from database. This prevented everyone from
working with multiple languages.
By not removing the field it gets indexed and provides a very basic way of implementing multiple
languages.
At least it's now possible to filter search results by current language for now. Still the records
are not "valid" as we do not add overlays for now.

This is a first step into full multi language support.

Martin Hummer already has a basic proof of concept, based on :ref:`concepts_dataprocessing` working,
depending on ``sys_language_uid``.

See :issue:`25`.
