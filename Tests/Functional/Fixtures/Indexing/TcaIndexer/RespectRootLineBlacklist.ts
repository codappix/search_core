plugin {
    tx_searchcore {
        settings {
            indexing {
                tt_content {
                    rootLineBlacklist = 3
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
