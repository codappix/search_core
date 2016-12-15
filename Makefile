mkfile_path := $(abspath $(lastword $(MAKEFILE_LIST)))
current_dir := $(dir $(mkfile_path))

TYPO3_WEB_DIR := $(current_dir).Build/Web
# Allow different versions on travis
TYPO3_VERSION ?= ~6.2.19
typo3DatabaseName ?= "test"
typo3DatabaseUsername ?= "dev"
typo3DatabasePassword ?= "dev"
typo3DatabaseHost ?= "127.0.0.1"

.PHONY: install
install: clean
	COMPOSER_PROCESS_TIMEOUT=1000 composer require -vv --dev --prefer-source typo3/cms="$(TYPO3_VERSION)"
	git checkout composer.json
	mkdir -p .Build/vendor/typo3/Packages/Libraries
	[ -L .Build/vendor/typo3/Packages/Libraries/autoload.php ] \
		|| cd .Build/vendor/typo3/Packages/Libraries \
		&& ln -snvf ../../../../vendor/autoload.php
	sed -i '22i\putenv("TYPO3_COMPOSER_AUTOLOAD=1");' .Build/Web/index.php

functionalTests:
	typo3DatabaseName=$(typo3DatabaseName) \
		typo3DatabaseUsername=$(typo3DatabaseUsername) \
		typo3DatabasePassword=$(typo3DatabasePassword) \
		typo3DatabaseHost=$(typo3DatabaseHost) \
		TYPO3_PATH_WEB=$(TYPO3_WEB_DIR) \
		.Build/bin/phpunit --colors --debug -v \
			-c Tests/Functional/FunctionalTests.xml

uploadCodeCoverage: uploadCodeCoverageToScrutinizer uploadCodeCoverageToCodacy

uploadCodeCoverageToScrutinizer:
	wget https://scrutinizer-ci.com/ocular.phar && \
	php ocular.phar code-coverage:upload --format=php-clover .Build/report/unit/clover/coverage && \
	php ocular.phar code-coverage:upload --format=php-clover .Build/report/functional/clover/coverage

uploadCodeCoverageToCodacy:
	composer require -vv --dev codacy/coverage && \
	git checkout composer.json && \
	php .Build/bin/codacycoverage clover .Build/report/unit/clover/coverage && \
	php .Build/bin/codacycoverage clover .Build/report/functional/clover/coverage

clean:
	rm -rf .Build composer.lock
