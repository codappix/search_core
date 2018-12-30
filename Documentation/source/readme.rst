TYPO3 Extension search_core
===========================

Introduction
============

What does it do?
----------------

Contrary to most search solutions, search_core is an ETL (=Extract, Transform, Load)
Framework. This allows to extract data from one source, transform it, and load them
into an target system. Focusing on search solutions, but not limited to them.

The provided process is to extract data from TYPO3 database storage using TCA, to
transform those data using data processors, and to load them into some search
storage like Elasticsearch. This is done via Hooks and CLI.

Also the process is to extract data from some storage like Elasticsearch, transform
the data using data processors and to load them into the TYPO3 frontend. This is done
via a Frontend Plugin.

Current state
-------------

The basic necessary features are already implemented. Still features like workspaces
or multi language are not provided out of the box.

Also only Elasticsearch is provided out of the box as a storage backend. But an
implementation for Algolia is already available via 3rd Party:
https://github.com/martinhummer/search_algolia

As the initial intend was to provide a common API and implementation for arbitrary
search implementations for TYPO3, the API is not fully implemented for ETL right now.
Also that's the reason for using "search_core" as extension name.
