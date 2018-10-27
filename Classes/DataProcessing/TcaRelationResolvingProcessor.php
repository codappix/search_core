<?php

namespace Codappix\SearchCore\DataProcessing;

/*
 * Copyright (C) 2018  Daniel Siepmann <coding@daniel-siepmann.de>
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

use Codappix\SearchCore\Domain\Index\TcaIndexer\RelationResolver;
use Codappix\SearchCore\Domain\Index\TcaIndexer\TcaTableServiceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Resolves relations from TCA using RelationResolver.
 */
class TcaRelationResolvingProcessor implements ProcessorInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var RelationResolver
     */
    protected $relationResolver;

    public function __construct(
        ObjectManagerInterface $objectManager,
        RelationResolver $relationResolver
    ) {
        $this->objectManager = $objectManager;
        $this->relationResolver = $relationResolver;
    }

    /**
     * @throws \InvalidArgumentException If _table is not configured.
     */
    public function processData(array $record, array $configuration): array
    {
        $this->initializeConfiguration($configuration);

        /** @var TcaTableServiceInterface $tcaTableService */
        $tcaTableService = $this->objectManager->get(
            TcaTableServiceInterface::class,
            $configuration['_table']
        );

        $processedRecord = $this->relationResolver->resolveRelationsForRecord(
            $tcaTableService,
            $this->getRecordToProcess($record, $configuration)
        );

        return array_merge($record, $processedRecord);
    }

    /**
     * @throws \InvalidArgumentException If _table is not configured.
     */
    protected function initializeConfiguration(array &$configuration)
    {
        if (!isset($configuration['_table'])) {
            throw new \InvalidArgumentException('The configuration "_table" is mandantory.', 1524552631);
        }

        if (!isset($configuration['excludeFields'])) {
            $configuration['excludeFields'] = '';
        }

        $configuration['excludeFields'] = GeneralUtility::trimExplode(',', $configuration['excludeFields'], true);
    }

    protected function getRecordToProcess(array $record, array $configuration): array
    {
        if ($configuration['excludeFields'] === []) {
            return $record;
        }

        $newRecord = [];
        $keysToUse = array_diff(array_keys($record), $configuration['excludeFields']);
        foreach ($keysToUse as $keyToUse) {
            $newRecord[$keyToUse] = $record[$keyToUse];
        }

        return $newRecord;
    }
}
