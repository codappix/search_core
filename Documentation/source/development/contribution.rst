.. _contribution:

Contribution
============

Everyone is welcome to contribute, whether it's code, issues, feature requests or any other kind.

Below is a documentation what to respect during contributions.

.. _contribution_setup:

Setup
-----

To start contributions regarding code, make sure your environment matches the following
requirements:

* composer is executable

* PHP on CLI is executable

* MySQL is up and running with user *dev* and password *dev* on *127.0.0.1* or to overwrite the
  environment variables, see :file:`Makefile`.
  And MySQL is not set to strict mode as TYPO3 doesn't support strict mode, see
  https://review.typo3.org/#/c/26725/3/INSTALL.md.

* Elasticsearch is installed and up and running on *localhost:9200*.

Then setup your system::

    git clone git@github.com:DanielSiepmann/search_core.git \
        && cd search_core \
        && export typo3DatabaseName="searchcoretest76" \
        && export TYPO3_VERSION="~8.7" \
        && make install \
        && make unitTests \
        && make functionalTests

If all tests are okay, start your work.

If you are working with multiple TYPO3 versions make sure to export `typo3DatabaseName` and
`TYPO3_VERSION` in your environment like::

    export typo3DatabaseName="searchcoretest62"
    export TYPO3_VERSION="~6.2"

Also run the install command for each version before running any tests. Only this will make sure you
are testing against the actual TYPO3 Version and database scheme.

.. _contribution_development:

Development
-----------

All changes are introduced through pull requests at `Github`_ and should contain the following:

* Adjusted tests if tests existed before. Otherwise they will break on `travis-ci`_.

* New tests whenever possible and useful.

* Code has to follow `PSR-2`_.

* Adjusted documentation.

* Make sure to follow the documented :ref:`concepts`.

.. _Github: https://github.com/DanielSiepmann/search_core
.. _travis-ci: https://travis-ci.org/
.. _PSR-2: http://www.php-fig.org/psr/psr-2/
