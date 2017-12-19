plugin {
    tx_searchcore {
        settings {
            connections {
                elasticsearch {
                    host = localhost
                    port = 9200
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

                    mapping {
                        CType {
                            type = keyword
                        }
                    }

                    dataProcessing {
                        0 = Codappix\SearchCore\DataProcessing\CopyToProcessor
                        0 {
                            from = abstract, description, bodytext
                            to = search_abstract
                        }
                    }
                }
            }

            searching {
                fields = search_title
                facets {
                    contentTypes {
                        field = CType
                    }
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
