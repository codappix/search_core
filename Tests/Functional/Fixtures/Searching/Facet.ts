plugin {
    tx_searchcore {
        settings {
            searching {
                facets {
                    contentTypes {
                        terms {
                            field = CType
                        }
                    }
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
