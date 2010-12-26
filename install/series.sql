--
-- series
--
CREATE TABLE series ( id   INTEGER PRIMARY KEY,
                              name TEXT NOT NULL COLLATE NOCASE,
                              sort TEXT COLLATE NOCASE,
                              UNIQUE (name)
                             );
CREATE INDEX series_idx ON series (name COLLATE NOCASE);
CREATE TRIGGER fkc_delete_on_series
BEFORE DELETE ON series
BEGIN
    SELECT CASE
        WHEN (SELECT COUNT(id) FROM books_series_link WHERE series=OLD.id) > 0
        THEN RAISE(ABORT, 'Foreign key violation: series is still referenced')
    END;
END;
CREATE TRIGGER series_insert_trg
        AFTER INSERT ON series
        BEGIN
          UPDATE series SET sort=NEW.name WHERE id=NEW.id;
        END;
CREATE TRIGGER series_update_trg
        AFTER UPDATE ON series
        BEGIN
          UPDATE series SET sort=NEW.name WHERE id=NEW.id;
        END;
