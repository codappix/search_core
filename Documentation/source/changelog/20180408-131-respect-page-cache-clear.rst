Feature 131 "Pages do not get indexed if content has changed"
=============================================================

Previously we only used DataHandler hooks triggered when processing records. This way we did not
index a page when content has changed.

We now also use cache clear hooks of DataHandler to index pages whenever their cache get cleared.
This way we also index a page if an integrator configured to clear further pages if content was
changed.

Still there are limitations. We do not get informed for pages which got cleared due to attached
caches via TypoScript.

See :issue:`131`.
