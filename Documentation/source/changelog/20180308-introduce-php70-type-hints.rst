Breaking Change "Introduce PHP 7.0 TypeHints"
=============================================

As PHP evolved, we now migrate the whole code base to use PHP 7.0 type hints.
We do not use PHP 7.1 Type Hints, as some customers still need PHP 7.0 support.

Also we added missing methods to interfaces, that were already used in code.

As this leads to changed method signatures, most custom implementations of interfaces, or overwrites
of existing methods are broken.

To fix, just update the signatures as pointed out by PHP while running the code.
