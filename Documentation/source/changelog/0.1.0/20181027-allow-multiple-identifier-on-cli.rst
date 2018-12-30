Breaking Change "Allow multiple identifiers on cli"
===================================================

All CLI commands except a comma separated list of IDs now. Still single IDs are allowed.

Each Identifier will be processed one by another. This is just for continence to not
call the command multiple times with different identifiers.

Spaces are ignored before and after commas.


As the argument was renamed from ``--identifier`` to ``--identifiers``, this is
considered a breaking change.
