Feature "Added delete documents command"
========================================

A new command to delete all documents within an index was added. In contrast to the
existing delete command, this deletes only documents but keeps the index.

E.g. if your backend is Elasticsearch or a relational database, the index or table is
kept, including structure or mappings, while only the documents or rows are removed.

In contrast the existing delete command will still remove the index or table itself,
depending on the used connection.
