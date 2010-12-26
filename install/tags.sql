--
-- Tags
--
CREATE TABLE tags ( id   INTEGER PRIMARY KEY,
                            name TEXT NOT NULL COLLATE NOCASE,
                            UNIQUE (name)
                             );
CREATE INDEX tags_idx ON tags (name COLLATE NOCASE);
CREATE TRIGGER fkc_delete_on_tags
BEFORE DELETE ON tags
BEGIN
    SELECT CASE
        WHEN (SELECT COUNT(id) FROM books_tags_link WHERE tag=OLD.id) > 0
        THEN RAISE(ABORT, 'Foreign key violation: tags is still referenced')
    END;
END;
