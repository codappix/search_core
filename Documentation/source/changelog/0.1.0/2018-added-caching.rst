Feature "Added caching"
=======================

Processing searches are now cacheable, the TYPO3 caching framework is used for
caching. By default the ``NullBackend`` is used to keep old behaviour, which matches
searches the best.

Still if the same search is run multiple times during a single request, the
``TransientMemoryBackend`` is a nice option.

Depending on search backend, one might also use a different backend for caching and
configure some TTL.

.. note::

   Paging is currently not supported for caching.

   Using ``NullBackend`` or ``TransientMemoryBackend`` will work, but using persistent
   backends in combination with fluid pagination widget will lead to errors right
   now.

For further information, check out official :ref:`t3explained:caching` documentation.
