--
-- Authors
--
CREATE TABLE authors ( id   INTEGER PRIMARY KEY,
                              name TEXT NOT NULL COLLATE NOCASE,
                              sort TEXT COLLATE NOCASE,
                              UNIQUE(name)
                             );

CREATE TRIGGER fkc_delete_on_authors
BEFORE DELETE ON authors
BEGIN
    SELECT CASE
        WHEN (SELECT COUNT(id) FROM books_authors_link WHERE author=OLD.id) > 0
        THEN RAISE(ABORT, 'Foreign key violation: authors is still referenced')
    END;
END;
