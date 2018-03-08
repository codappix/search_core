<?php
namespace Codappix\SearchCore\Connection\Elasticsearch;

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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Configuration\InvalidArgumentException;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory to get indexes.
 *
 * The factory will take care of configuration and creation of index if necessary.
 */
class IndexFactory implements Singleton
{
    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(ConfigurationContainerInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get an index bases on TYPO3 table name.
     */
    public function getIndex(Connection $connection, string $documentType) : \Elastica\Index
    {
        $index = $connection->getClient()->getIndex('typo3content');

        if ($index->exists() === false) {
            $index->create($this->getConfigurationFor($documentType));
        }

        return $index;
    }

    protected function getConfigurationFor(string $documentType) : array
    {
        try {
            $configuration = $this->configuration->get('indexing.' . $documentType . '.index');

            if (isset($configuration['analysis']['analyzer'])) {
                foreach ($configuration['analysis']['analyzer'] as $key => $analyzer) {
                    $configuration['analysis']['analyzer'][$key] = $this->prepareAnalyzerConfiguration($analyzer);
                }
            }

            return $configuration;
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }

    protected function prepareAnalyzerConfiguration(array $analyzer) : array
    {
        $fieldsToExplode = ['char_filter', 'filter'];

        foreach ($fieldsToExplode as $fieldToExplode) {
            if (isset($analyzer[$fieldToExplode])) {
                $analyzer[$fieldToExplode] = GeneralUtility::trimExplode(',', $analyzer[$fieldToExplode], true);
            }
        }

        return $analyzer;
    }
}
