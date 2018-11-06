Feature 170 "Do not specify the pluginName in ConfigurationContainer"
===============================================================================

Prior to the change it was not possible to create a second plugin in a
separate extension. The pluginName for the configuration was set to `search`.
The problem was that the plugin specific settings could not be fetched.

The configuration in `plugin.tx_exampleextension_pluginkey.settings {..}` and
from flexform were not fetched.

Now the pluginName is not set and the ConfigurationManager checks which plugin
is used in the current context.

It is now possible to create a second plugin. For example if you want to cache
the output of your query or the filters you specified.

See :issue:`170`.
