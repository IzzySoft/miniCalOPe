--
-- publishers
--
CREATE TABLE publishers ( id   INTEGER PRIMARY KEY,
                                  name TEXT NOT NULL COLLATE NOCASE,
                                  sort TEXT COLLATE NOCASE,
                                  UNIQUE(name)
                             );
CREATE INDEX publishers_idx ON publishers (name COLLATE NOCASE);
CREATE TRIGGER fkc_delete_on_publishers
BEFORE DELETE ON publishers
BEGIN
    SELECT CASE
        WHEN (SELECT COUNT(id) FROM books_publishers_link WHERE publisher=OLD.id) > 0
        THEN RAISE(ABORT, 'Foreign key violation: publishers is still referenced')
    END;
END;
