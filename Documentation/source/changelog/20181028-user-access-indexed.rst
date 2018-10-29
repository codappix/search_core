Feature "Added frontend user authentication access"
===============================================================

Indexation added based on page access via ``fe_group`` and inherited
from ```extendToSubpages```.

The searching is added via typoscript using the UserFunc filter::

    frontendUserAccess {
        type = user
        userFunc = Codappix\SearchCore\Hook\Filter\FrontendUserAccessFilter->generate
    }

To bypass this filter simply unset default filter in searching.filter
