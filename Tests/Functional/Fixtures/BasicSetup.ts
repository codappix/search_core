plugin {
    tx_searchcore {
        settings {
            connection {
                host = localhost
                port = 9200
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
