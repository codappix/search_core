plugin {
    tx_searchcore {
        settings {
            connections {
                elasticsearch {
                    host = localhost
                    port = 9200
                    index = typo3content
                }
            }

            indexing {
                tt_content {
                    indexer = Codappix\SearchCore\Domain\Index\TcaIndexer

                    additionalWhereClause (
                        tt_content.CType NOT IN ('gridelements_pi1', 'list', 'div', 'menu', 'shortcut', 'search', 'login')
                        AND tt_content.bodytext != ''
                    )

                    mapping {
                        CType {
                            type = keyword
                        }
                    }
                }

                pages {
                    indexer = Codappix\SearchCore\Domain\Index\TcaIndexer\PagesIndexer
                    abstractFields = abstract, description, bodytext

                    mapping {
                        CType {
                            type = keyword
                        }
                    }
                }
            }

            searching {
                facets {
                    contentTypes {
                        field = CType
                    }
                }

                fields {
                    query = _all
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
