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
use Elastica\Exception\ResponseException;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * Inject log manager to get concrete logger from it.
     *
     * @param \TYPO3\CMS\Core\Log\LogManager $logManager
     */
    public function injectLogger(\TYPO3\CMS\Core\Log\LogManager $logManager)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
    }

    /**
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(ConfigurationContainerInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get an index bases on TYPO3 table name.
     *
     * @param Connection $connection
     * @param string $documentType
     *
     * @return \Elastica\Index
     */
    public function getIndex(Connection $connection, $documentType)
    {
        $index = $connection->getClient()->getIndex('typo3content');

        if ($index->exists() === false) {
            $config = $this->getConfigurationFor($documentType);
            $this->logger->debug(sprintf('Create index %s.', $documentType), [$documentType, $config]);
            $index->create($config);
            $this->logger->debug(sprintf('Created index %s.', $documentType), [$documentType]);
        }

        return $index;
    }

    /**
     * @param string $documentType
     *
     * @return array
     */
    protected function getConfigurationFor($documentType)
    {
        try {
            $configuration = $this->configuration->get('indexing.' . $documentType . '.index');

            foreach (['analyzer', 'filter'] as $optionsToExpand) {
                if (isset($configuration['analysis'][$optionsToExpand])) {
                    foreach ($configuration['analysis'][$optionsToExpand] as $key => $options) {
                        $configuration['analysis'][$optionsToExpand][$key] = $this->prepareOptions($options);
                    }
                }
            }

            return $configuration;
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function prepareOptions(array $options)
    {
        $fieldsToExplode = ['char_filter', 'filter', 'word_list'];

        foreach ($fieldsToExplode as $fieldToExplode) {
            if (isset($options[$fieldToExplode])) {
                $options[$fieldToExplode] = GeneralUtility::trimExplode(',', $options[$fieldToExplode], true);
            }
        }

        return $options;
    }
}
