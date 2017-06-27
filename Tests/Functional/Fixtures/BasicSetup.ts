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
                    indexer = Leonmrni\SearchCore\Domain\Index\TcaIndexer
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
