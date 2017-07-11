plugin {
    tx_searchcore {
        settings {
            indexing {
                tt_content {
                    additionalWhereClause = tt_content.CType NOT IN ('div')
                }
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore
