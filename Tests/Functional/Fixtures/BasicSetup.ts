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
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
