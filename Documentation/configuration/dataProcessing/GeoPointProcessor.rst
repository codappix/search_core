``Codappix\SearchCore\DataProcessing\GeoPointProcessor``
========================================================

Will create a new field, ready to use as GeoPoint field for Elasticsearch.

Possible Options:

``to``
    Defines the field to create as GeoPoint.
``lat``
    Defines the field containing the latitude.
``lon``
    Defines the field containing the longitude.

Example::

    plugin.tx_searchcore.settings.indexing.tt_content.dataProcessing {
        1 = Codappix\SearchCore\DataProcessing\GeoPointProcessor
        1 {
            to = location
            lat = lat
            lon = lng
        }
    }

The above example will create a new field ``location`` as GeoPoint with latitude fetched from field
``lat`` and longitude fetched from field ``lng``.
