<?php

namespace Codappix\SearchCore\Domain\Index\TcaIndexer;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
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
use Codappix\SearchCore\Connection\ConnectionInterface;
use Codappix\SearchCore\Domain\Index\TcaIndexer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Specific indexer for Pages, will basically add content of page.
 */
class PagesIndexer extends TcaIndexer
{
    /**
     * @var TcaTableServiceInterface
     */
    protected $contentTableService;

    /**
     * @var \TYPO3\CMS\Core\Resource\FileRepository
     * @inject
     */
    protected $fileRepository;

    /**
     * @param TcaTableServiceInterface $tcaTableService
     * @param TcaTableServiceInterface $contentTableService
     * @param ConnectionInterface $connection
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(
        TcaTableServiceInterface $tcaTableService,
        TcaTableServiceInterface $contentTableService,
        ConnectionInterface $connection,
        ConfigurationContainerInterface $configuration
    ) {
        parent::__construct($tcaTableService, $connection, $configuration);
        $this->contentTableService = $contentTableService;
    }

    /**
     * @param array $record
     */
    protected function prepareRecord(array &$record)
    {
        parent::prepareRecord($record);

        // Override access from parent rootline
        $record['search_access'] = $this->fetchAccess($record['uid'], (array)$record['search_access']);

        $possibleTitleFields = ['nav_title', 'tx_tqseo_pagetitle_rel', 'title'];
        foreach ($possibleTitleFields as $searchTitleField) {
            if (isset($record[$searchTitleField]) && trim($record[$searchTitleField])) {
                $record['search_title'] = trim($record[$searchTitleField]);
                break;
            }
        }

        $record['media'] = $this->fetchMediaForPage($record['uid']);
        $content = $this->fetchContentForPage($record['uid']);
        if ($content !== []) {
            $record['content'] = $content['content'];
            $record['media'] = array_values(array_unique(array_merge($record['media'], $content['images'])));
        }
    }

    /**
     * @param integer $uid
     * @return array
     */
    protected function fetchContentForPage(int $uid): array
    {
        if ($this->contentTableService instanceof TcaTableService) {
            $queryBuilder = $this->contentTableService->getQuery();
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    $this->contentTableService->getTableName() . '.pid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            );
            $contentElements = $queryBuilder->execute()->fetchAll();
        } else {
            $contentElements = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                $this->contentTableService->getFields(),
                $this->contentTableService->getTableClause(),
                $this->contentTableService->getWhereClause() .
                sprintf(' AND %s.pid = %u', $this->contentTableService->getTableName(), $uid)
            );
        }

        if ($contentElements === null) {
            $this->logger->debug('No content for page ' . $uid);
            return [];
        }

        $this->logger->debug('Fetched content for page ' . $uid);
        $images = [];
        $content = [];
        foreach ($contentElements as $contentElement) {
            $images = array_merge(
                $images,
                $this->getContentElementImages($contentElement['uid'])
            );
            $content[] = $this->getContentFromContentElement($contentElement);
        }

        return [
            // Remove Tags.
            // Interpret escaped new lines and special chars.
            // Trim, e.g. trailing or leading new lines.
            'content' => trim(stripcslashes(strip_tags(implode(' ', $content)))),
            'images' => $images,
        ];
    }

    /**
     * @param integer $uidOfContentElement
     * @return array
     */
    protected function getContentElementImages(int $uidOfContentElement): array
    {
        return $this->fetchSysFileReferenceUids($uidOfContentElement, 'tt_content', 'image');
    }

    /**
     * @param integer $uid
     * @return array
     */
    protected function fetchMediaForPage(int $uid): array
    {
        return $this->fetchSysFileReferenceUids($uid, 'pages', 'media');
    }

    /**
     * @param integer $uid
     * @param array $pageAccess
     * @return array
     */
    protected function fetchAccess(int $uid, array $pageAccess): array
    {
        try {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $rootline = $objectManager->get(RootlineUtility::class, $uid)->get();
        } catch (\RuntimeException $e) {
            $this->logger->notice(
                sprintf('Could not fetch rootline for page %u, because: %s', $uid, $e->getMessage()),
                [$pageAccess, $e]
            );
            return $pageAccess;
        }

        $access = [$pageAccess];
        $extended = false;
        foreach ($rootline as $pageInRootLine) {
            if ($pageInRootLine['extendToSubpages'] && (!empty($pageInRootLine['fe_group']))) {
                $extended = true;
                $access[] = GeneralUtility::intExplode(
                    ',',
                    $pageInRootLine['fe_group'],
                    true
                );
            }
        }

        // Return combined rootline extended access and return unique id's
        $access = array_unique(array_merge(...$access));

        // Remove public value if fe_group is extended to this page
        if ($extended && ($key = array_search(0, $access, true)) !== false) {
            unset($access[$key]);
        }
        return array_values($access);
    }

    /**
     * @param integer $uid
     * @param string $tablename
     * @param string $fieldname
     * @return array
     */
    protected function fetchSysFileReferenceUids(int $uid, string $tablename, string $fieldname): array
    {
        $imageRelationUids = [];
        $imageRelations = $this->fileRepository->findByRelation($tablename, $fieldname, $uid);

        foreach ($imageRelations as $relation) {
            $imageRelationUids[] = $relation->getUid();
        }

        return $imageRelationUids;
    }

    /**
     * @param array $contentElement
     * @return string
     */
    protected function getContentFromContentElement(array $contentElement): string
    {
        $content = '';

        $fieldsWithContent = GeneralUtility::trimExplode(
            ',',
            $this->configuration->get('indexing.' . $this->identifier . '.contentFields'),
            true
        );
        foreach ($fieldsWithContent as $fieldWithContent) {
            if (isset($contentElement[$fieldWithContent]) && trim($contentElement[$fieldWithContent])) {
                $content .= trim($contentElement[$fieldWithContent]) . ' ';
            }
        }

        return trim($content);
    }
}
