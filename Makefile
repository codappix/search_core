mkfile_path := $(abspath $(lastword $(MAKEFILE_LIST)))
current_dir := $(dir $(mkfile_path))

TYPO3_WEB_DIR := $(current_dir).Build/Web
# Allow different versions on travis
TYPO3_VERSION ?= ~6.2

.PHONY: install
install:
	rm -rf .Build

	composer require --dev --prefer-source typo3/cms="$(TYPO3_VERSION)"
	composer update -vv

	git checkout composer.json
	mkdir -p $(TYPO3_WEB_DIR)/uploads $(TYPO3_WEB_DIR)/typo3temp

.PHONY: Tests
Tests:
	TYPO3_PATH_WEB=$(TYPO3_WEB_DIR) .Build/bin/phpunit --colors --debug -v -c Tests/Unit/UnitTests.xml
