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

    git clone git@github.com:codappix/search_core.git \
        && cd search_core \
        && export typo3DatabaseName="searchcoretest" \
        && make install \
        && make unitTests \
        && make functionalTests \
        && make cgl

If all tests are okay, start your work.

.. _contribution_development:

Development
-----------

All changes are introduced through pull requests at `GitHub`_ and should contain the following:

* Adjusted tests if tests existed before. Otherwise they will break on `travis-ci`_.

* New tests whenever possible and useful.

* Code has to follow `PSR-2`_.

* Adjusted documentation.

* Make sure to follow the documented :ref:`concepts`.

.. _GitHub: https://github.com/codappix/search_core
.. _travis-ci: https://travis-ci.org/
.. _PSR-2: http://www.php-fig.org/psr/psr-2/
