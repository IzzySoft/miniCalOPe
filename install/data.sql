--
-- data
--
CREATE TABLE data ( id     INTEGER PRIMARY KEY,
                            book   INTEGER NON NULL,
                            format TEXT NON NULL COLLATE NOCASE,
                            uncompressed_size INTEGER NON NULL,
                            name TEXT NON NULL,
                            UNIQUE(book, format)
);
CREATE INDEX data_idx ON data (book);
CREATE TRIGGER fkc_data_insert
        BEFORE INSERT ON data
        BEGIN
            SELECT CASE
                WHEN (SELECT id from books WHERE id=NEW.book) IS NULL
                THEN RAISE(ABORT, 'Foreign key violation: book not in books')
            END;
        END;
CREATE TRIGGER fkc_data_update
        BEFORE UPDATE OF book ON data
        BEGIN
            SELECT CASE
                WHEN (SELECT id from books WHERE id=NEW.book) IS NULL
                THEN RAISE(ABORT, 'Foreign key violation: book not in books')
            END;
        END;
