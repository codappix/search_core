# Configure extension
plugin {
    tx_searchcore {
        settings {
            connection {
                host = localhost
                port = 9200
            }

            index {
                allowedTables = tt_content
            }
        }
    }
}

module.tx_searchcore < plugin.tx_searchcore


# Insert basic output to allow testing of frontend plugins
page = PAGE
page {
    10 = USER
    10 {
        userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
        extensionName = SearchCore
        pluginName = search
        vendorName = Leonmrni
    }
}
