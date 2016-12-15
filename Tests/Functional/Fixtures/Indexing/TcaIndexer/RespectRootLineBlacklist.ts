plugin {
    tx_searchcore {
        settings {
            index {
                rootLineBlacklist = 3
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
