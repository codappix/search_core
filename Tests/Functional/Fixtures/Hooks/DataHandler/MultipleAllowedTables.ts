plugin {
    tx_searchcore {
        settings {
            indexer {
                tca {
                    allowedTables = tt_content, fe_user
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
