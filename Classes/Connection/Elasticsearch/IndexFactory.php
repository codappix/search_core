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

use Elastica\Exception\ResponseException;
use TYPO3\CMS\Core\SingletonInterface as Singleton;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Factory to get indexes.
 *
 * The factory will take care of configuration and creation of index if necessary.
 */
class IndexFactory implements Singleton
{
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
        // TODO: Fetch index name from configuration, based on $documentType.
        $index = $connection->getClient()->getIndex('typo3content');

        try {
            // TODO: Provide configuration?!
            // http://elastica.io/getting-started/storing-and-indexing-documents.html#section-analysis
            $index->create();
        } catch (ResponseException $exception) {
            if (stripos($exception->getMessage(), 'already exists') === false) {
                throw $exception;
            }
        }

        return $index;
    }
}
