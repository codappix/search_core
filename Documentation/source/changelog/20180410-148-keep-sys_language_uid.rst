Feature 148 "Cast sys_language_uid to int"
==========================================

While resolving relations, the configured language uid field, fetched from TCA, will
be casted to integer and returned immediately.

This change prevents the bug mentioned in :issue:`148`, where `0` is casted to an
empty string, which makes filtering hard.

See :issue:`148`.
