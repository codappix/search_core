<?php
namespace Leonmrni\SearchCore\Domain\Search;

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

use Leonmrni\SearchCore\Connection\ConnectionInterface;
use Leonmrni\SearchCore\Connection\Elasticsearch\Query;
use Leonmrni\SearchCore\Connection\SearchRequestInterface;

class QueryFactory
{
    /**
     * @param ConnectionInterface $connection
     * @param SearchRequestInterface $searchRequest
     *
     * @return \Elastica\Query
     */
    public function create(
        ConnectionInterface $connection,
        SearchRequestInterface $searchRequest
    ) {
        return $this->createElasticaQuery($searchRequest);
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return \Elastica\Query
     */
    protected function createElasticaQuery(SearchRequestInterface $searchRequest)
    {
        $query = [
            'bool' => [
                'must' => [
                    [
                        'match' => [
                            '_all' => $searchRequest->getSearchTerm()
                        ],
                    ],
                ],
            ],
        ];
        $queryFilter = [];

        if ($searchRequest->hasFilter()) {
            foreach ($searchRequest->getFilter() as $field => $value) {
                $queryFilter[$field] = $value;
            }
            $query['bool']['filter'] = [
                'term' => $queryFilter,
            ];
        }

        return new \Elastica\Query(['query' => $query]);
    }
}
