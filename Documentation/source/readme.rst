TYPO3 Extension search_core's documentation!
============================================

Introduction
============

What does it do?
----------------

The goal of this extension is to provide search integrations into TYPO3 CMS. The extension will
abstract the concrete implementations to allow exchange of concrete backends like Elasticsearch or
solr.

The extension provides integration into TYPO3 like a frontend plugin for searches and hooks to
update search indexes on updates. Also a command line interface is provided for interactions like
reindexing.

Current state
-------------

This is still a very early alpha version. More information can be taken from Github at
`current issues`_ and `current projects`_.

We are also focusing on Code Quality and Testing through `travis ci`_, `scrutinizer`_ and `codacy`_.

.. _current issues: https://github.com/DanielSiepmann/search_core/issues
.. _current projects: https://github.com/DanielSiepmann/search_core/projects
.. _travis ci: https://travis-ci.org/DanielSiepmann/search_core
.. _scrutinizer: https://scrutinizer-ci.com/g/DanielSiepmann/search_core/inspections
.. _codacy: https://www.codacy.com/app/daniel-siepmann/search_core/dashboard

