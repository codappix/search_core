.. _configuration_options_search:

Searching
=========

.. _size:

size
----

Defined how many search results should be fetched to be available in search result.

Example::

    plugin.tx_searchcore.settings.searching.size = 50

Default is ``10``.

.. _facets:

facets
------

Define aggregations for Elasticsearch, have a look at the official docs: https://www.elastic.co/guide/en/elasticsearch/reference/5.2/search-aggregations-bucket-terms-aggregation.html

Example::

    category {
        terms {
            field = categories
        }
    }

    month {
        date_histogram {
            field = released
            interval = month
            format = Y-MM-01
            order {
                _time = desc
            }
        }
    }


The above example will provide a facet with options for all found ``categories`` results together
with a count. Also a facet for ``released`` will be provided.

.. _filter:

filter
------

Define filter that should be set for all search requests.

Example::

    plugin.tx_searchcore.settings.searching.filter {
        property = value
    }

Also see :ref:`mapping.filter` to map incoming request information, e.g. from a ``select``, to build
more complex filters.

For Elasticsearch the fields have to be filterable, e.g. need a mapping as ``keyword``.

.. _minimumShouldMatch:

minimumShouldMatch
------------------

Define the minimum match for Elasticsearch, have a look at the official docs:
https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-minimum-should-match.html

Example::

    plugin.tx_searchcore.settings.searching.minimumShouldMatch = 50%

.. _boost:

boost
-----

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

Define a field to use as a factor for scoring. The configuration is passed through to Elasticsearch
``field_value_factor``, see:
https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-function-score-query.html#function-field-value-factor

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

   <f:select name="searchRequest[filter][month][from]">
       <f:select.option value="">Month</f:select.option>
       <f:for each="{searchResult.facets.month.options}" as="month">
           <f:if condition="{month.count}">
               <f:select.option
                   value="{month.displayName -> f:format.date(format: 'Y-m')}"
                   selected="{f:if(condition: '{searchRequest.filter.month.from} == {month.displayName -> f:format.date(format: \'Y-m\')}', then: 1, else: 0)}"
               >{month.displayName -> f:format.date(format: '%B %Y')}</f:select.option>
           </f:if>
       </f:for>
   </f:select>
   <f:select name="searchRequest[filter][month][to]">
       <f:select.option value="">Month</of:select.ption>
       <f:for each="{searchResult.facets.month.options}" as="month">
           <f:if condition="{month.count}">
               <f:select.option
                   value="{month.displayName -> f:format.date(format: 'Y-m')}"
                   selected="{f:if(condition: '{searchRequest.filter.month.from} == {month.displayName -> f:format.date(format: \'Y-m\')}', then: 1, else: 0)}"
               >{month.displayName -> f:format.date(format: '%B %Y')}</f:select.option>
           </f:if>
       </f:for>
   </f:select>

This will create a ``month`` filter with sub properties. To make this filter actually work, you
can add the following TypoScript, which will be added to the filter::

    mapping {
        filter {
            month {
                type = range
                field = released
                raw {
                    format = yyyy-MM
                }

                fields {
                    gte = from
                    lte = to
                }
            }
        }
    }

``fields`` has a special meaning here. This will actually map the properties of the filter to fields
in Elasticsearch. On the left hand side is the Elasticsearch field name, on the right side the one
submitted as a filter.

The ``field``, in above example ``released``, will be used as the Elasticsearch field for
filtering. This way you can use arbitrary filter names and map them to existing Elasticsearch fields.

Everything that is configured inside ``raw`` is passed, as is, to search service, e.g.
Elasticsearch.

.. _fields:

fields
------

Defines the fields to fetch and search from Elasticsearch. With the following sub keys:

``query`` defines the fields to search in. Configure a comma separated list of fields to search in.
This is necessary if you have configured special mapping for some fields, or just want to search
some fields. The following is an example configuration::

    fields {
        query = _all, city
    }

The following sub properties configure the fields to fetch from Elasticsearch:

First ``stored_fields`` which is a list of comma separated fields which actually exist and will be
added. Typically you will use ``_source`` to fetch the whole indexed fields.

Second is ``script_fields``, which allow you to configure scripted fields for Elasticsearch.
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
Elasticsearch, except each property is run through Fluidtemplate, to allow you to use information
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

Only ``filter`` is allowed as value, as ``search`` is default behaviour. Using ``filter`` will
trigger a search to provide data while visiting the page, possible :ref:`filter` allow you to build
pages like "News".

.. _searching_dataprocessing:

dataProcessing
--------------

Configure modifications on each document before returning search result.
For full documentation check out :ref:`dataprocessors`.
