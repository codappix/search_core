.. highlight:: bash

.. _contribution:

Contribution
============

Everyone is welcome to contribute, whether it's code, issues, feature requests or any other kind.

Below is a documentation what to respect during contributions.

.. _contribution_setup:

Setup
-----

To start contributions regarding code, make sure to setup your system::

    git clone git@github.com:DanielSiepmann/search_core.git \
        && cd search_core \
        && make install \
        && make functionalTests

If all tests are okay, start your work.

Of course you might need some requirements like running elasticsearch and composer to work before.

.. _contribution_development:

Development
-----------

All changes are introduced through pull requests at `Github`_ and should contain the following:

* Adjusted tests if tests existed before. Otherwise they will break on `travis-ci`_.

* New tests whenever possible and usefull.

* Code has to follow `PSR-2`_.

* Adjusted documentation.

* Make sure to follow the documented :ref:`concepts`.

.. _Github: https://github.com/DanielSiepmann/search_core
.. _travis-ci: https://travis-ci.org/
.. _PSR-2: http://www.php-fig.org/psr/psr-2/
