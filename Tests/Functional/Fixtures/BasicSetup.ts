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
                        AND (tt_content.bodytext != '' OR tt_content.header != '')
                    )

                    mapping {
                        CType {
                            type = keyword
                        }
                    }

                    dataProcessing {
                        1 = Codappix\SearchCore\DataProcessing\TcaRelationResolvingProcessor
                    }
                }

                pages {
                    indexer = Codappix\SearchCore\Domain\Index\TcaIndexer\PagesIndexer
                    abstractFields = abstract, description, bodytext
                    contentFields = header, bodytext

                    mapping {
                        CType {
                            type = keyword
                        }
                    }

                    dataProcessing {
                        1 = Codappix\SearchCore\DataProcessing\TcaRelationResolvingProcessor
                    }
                }
            }

            searching {
                fields {
                    query = _all
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
