TYPO3 Extension search_core
===========================

Introduction
============

What does it do?
----------------

The goal of this extension is to provide search integrations into TYPO3 CMS. The extension will
provide a convenient API to allow developers to provide concrete implementations of backends like
Elasticsearch, Algolia or Solr.

The extension provides integration into TYPO3 like a frontend plugin for searches and hooks to
update search indexes on updates. Also a command line interface is provided for interactions like
re-indexing.

Current state
-------------

This is still a very early beta version. More information can be taken from Github at
`current issues`_.

We are also focusing on Code Quality and Testing through `travis ci`_, ``phpcs``, ``phpunit`` and
``phpstan``.

.. _current issues: https://github.com/Codappix/search_core/issues
.. _travis ci: https://travis-ci.org/Codappix/search_core
