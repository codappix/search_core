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
install:
	rm .Build
	COMPOSER_PROCESS_TIMEOUT=1000 composer require -vvv --dev typo3/cms="$(TYPO3_VERSION)"

	git checkout composer.json
	mkdir -p $(TYPO3_WEB_DIR)/uploads $(TYPO3_WEB_DIR)/typo3temp

unitTests:
	TYPO3_PATH_WEB=$(TYPO3_WEB_DIR) \
		.Build/bin/phpunit --colors --debug -v \
			-c Tests/Unit/UnitTests.xml

functionalTests:
	typo3DatabaseName=$(typo3DatabaseName) \
		typo3DatabaseUsername=$(typo3DatabaseUsername) \
		typo3DatabasePassword=$(typo3DatabasePassword) \
		typo3DatabaseHost=$(typo3DatabaseHost) \
		TYPO3_PATH_WEB=$(TYPO3_WEB_DIR) \
		.Build/bin/phpunit --colors --debug -v \
			-c Tests/Functional/FunctionalTests.xml

.PHONY: Tests
Tests: unitTests functionalTests
