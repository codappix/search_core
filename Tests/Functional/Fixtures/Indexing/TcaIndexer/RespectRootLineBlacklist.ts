plugin {
    tx_searchcore {
        settings {
            indexer {
                tca {
                    rootLineBlacklist = 3
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
