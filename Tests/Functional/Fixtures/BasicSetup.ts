plugin {
    tx_searchcore {
        settings {
            connections {
                elasticsearch {
                    host = localhost
                    port = 9200
                }
            }

            indexer {
                tca {
                    allowedTables = tt_content
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
