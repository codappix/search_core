<?php
namespace Leonmrni\SearchCore\Tests\Functional;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Core\Tests\FunctionalTestCase as CoreTestCase;

/**
 * All functional tests should extend this base class.
 */
abstract class AbstractFunctionalTestCase extends CoreTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/search_core'];

    public function setUp()
    {
        parent::setUp();

        $this->setUpBackendUserFromFixture(1);
        \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->initializeLanguageObject();

        foreach ($this->getDataSets() as $fixture) {
            $this->importDataSet($fixture);
        }

        foreach ($this->getPhpFixtures() as $fixture) {
            $this->loadPhpFixture($fixture);
        }

        $this->setUpFrontendRootPage(1, $this->getTypoScriptFilesForFrontendRootPage());
    }

    /**
     * Overwrite to import different files.
     *
     * Defines which DataSet Files should be imported.
     *
     * @return array<String>
     */
    protected function getDataSets()
    {
        return ['Tests/Functional/Fixtures/BasicSetup.xml'];
    }

    /**
     * Overwrite to import different files.
     *
     * Defines which PHP Files should be imported.
     *
     * @return array<String>
     */
    protected function getPhpFixtures()
    {
        return [];
    }

    /**
     * Overwrite to import different files.
     *
     * Defines which TypoScript Files should be imported.
     *
     * @return array<String>
     */
    protected function getTypoScriptFilesForFrontendRootPage()
    {
        return ['EXT:search_core/Tests/Functional/Fixtures/BasicSetup.ts'];
    }

    /**
     * Requires the given PHP file as fixture.
     *
     * @param string $phpFixture The file name relative to extension root.
     */
    protected function loadPhpFixture($phpFixture)
    {
        $fileToLoad = realpath(__DIR__ . '/../..') . '/' . $phpFixture;

        if (!is_file($fileToLoad)) {
            throw new \Exception(
                'PHP Fixture not found: "' . $phpFixture . '", tried to require file: "' . $fileToLoad . '".',
                1482418436
            );
        }

        require $phpFixture;
    }
}
