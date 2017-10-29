.. _configuration_options_search:

Searching
=========

.. _size:

size
----

Used by: Elasticsearch connection while building search query.

Defined how many search results should be fetched to be available in search result.

Example::

    plugin.tx_searchcore.settings.searching.size = 50

Default if not configured is 10.

.. _facets:

facets
------

Used by: Elasticsearch connection while building search query.

Define aggregations for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/search-aggregations-bucket-terms-aggregation.html
Currently only the term facet is provided.

Example::

    plugin.tx_searchcore.settings.searching.facets {
        contentTypes {
            field = CType
        }
    }

The above example will provide a facet with options for all found ``CType`` results together
with a count.

.. _filter:

filter
------

Used by: While building search request.

Define filter that should be set for all requests.

Example::

    plugin.tx_searchcore.settings.searching.filter {
        property = value
    }

For Elasticsearch the fields have to be filterable, e.g. need a mapping as ``keyword``.

.. _minimumShouldMatch:

minimumShouldMatch
------------------

Used by: Elasticsearch connection while building search query.

Define the minimum match for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-minimum-should-match.html

Example::

    plugin.tx_searchcore.settings.searching.minimumShouldMatch = 50%

.. _boost:

boost
-----

Used by: Elasticsearch connection while building search query.

Define fields that should boost the score for results.

Example::

    plugin.tx_searchcore.settings.searching.boost {
        search_title = 3
        search_abstract = 1.5
    }

For further information take a look at
https://www.elastic.co/guide/en/elasticsearch/guide/2.x/_boosting_query_clauses.html

.. _fieldValueFactor:

fieldValueFactor
----------------

Used by: Elasticsearch connection while building search query.

Define a field to use as a factor for scoring. The configuration is passed through to elastic
search ``field_value_factor``, see: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-function-score-query.html#function-field-value-factor

Example::

    plugin.tx_searchcore.settings.searching.field_value_factor {
        field = rootlineLevel
        modifier = reciprocal
        factor = 2
        missing = 1
    }

.. _mapping.filter:

mapping.filter
--------------

Allows to configure filter more in depth. If a filter with the given key exists, the TypoScript will
be added.

E.g. you submit a filter in form of:

.. code-block:: html

    <f:form.textfield property="filter.distance.location.lat" value="51.168098" />
    <f:form.textfield property="filter.distance.location.lon" value="6.381384" />
    <f:form.textfield property="filter.distance.distance" value="100km" />

This will create a ``distance`` filter with subproperties. To make this filter actually work, you
can add the following TypoScript, which will be added to the filter::

    mapping {
        filter {
            distance {
                field = geo_distance
                fields {
                    distance = distance
                    location = location
                }
            }
        }
    }

``fields`` has a special meaning here. This will actually map the properties of the filter to fields
in elasticsearch. In above example they do match, but you can also use different names in your form.
On the left hand side is the elasticsearch field name, on the right side the one submitted as a
filter.

The ``field``, in above example ``geo_distance``, will be used as the elasticsearch field for
filtering. This way you can use arbitrary filter names and map them to existing elasticsearch fields.

.. _fields:

fields
------

Defines the fields to fetch from elasticsearch. Two sub entries exist:

First ``stored_fields`` which is a list of comma separated fields which actually exist and will be
added. Typically you will use ``_source`` to fetch the whole indexed fields.

Second is ``script_fields``, which allow you to configure scripted fields for elasticsearch.
An example might look like the following::

    fields {
        script_fields {
            distance {
                condition = {request.filter.distance.location}
                script {
                    params {
                        lat = {request.filter.distance.location.lat -> f:format.number()}
                        lon = {request.filter.distance.location.lon -> f:format.number()}
                    }
                    lang = painless
                    inline = doc["location"].arcDistance(params.lat,params.lon) * 0.001
                }
            }
        }
    }

In above example we add a single ``script_field`` called ``distance``. We add a condition when this
field should be added. The condition will be parsed as Fluidtemplate and is casted to bool via PHP.
If the condition is true, or no ``condition`` exists, the ``script_field`` will be added to the
query. The ``condition`` will be removed and everything else is submitted one to one to
elasticsearch, except each property is run through Fluidtemplate, to allow you to use information
from search request, e.g. to insert latitude and longitude from a filter, like in the above example.

.. _sort:

sort
----

Sort is handled like :ref:`fields`.

.. _mode:

mode
----

Used by: Controller while preparing action.

Define to switch from search to filter mode.

Example::

    plugin.tx_searchcore.settings.searching {
        mode = filter
    }

Only ``filter`` is allowed as value. Will submit an empty query to switch to filter mode.
