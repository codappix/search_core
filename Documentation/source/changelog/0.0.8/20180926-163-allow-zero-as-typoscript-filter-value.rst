Bugfix 163 "It's not possible to configure a filter via TS with value 0 - zero"
===============================================================================

Prior to the change it was not possible to define a filter while searching, via
TypoScript, with the value `0`. `0` was filtered as empty value.

Now the configured filter is no longer filtered, it's up to the integrator to provide
proper configuration. Therefore `0` is now a valid and respected filter value.

See :issue:`163`.
