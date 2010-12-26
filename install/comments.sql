--
-- comments
--
CREATE TABLE comments ( id INTEGER PRIMARY KEY,
                              book INTEGER NON NULL,
                              text TEXT NON NULL COLLATE NOCASE,
                              UNIQUE(book)
                            );
CREATE INDEX comments_idx ON comments (book);
CREATE TRIGGER fkc_comments_insert
        BEFORE INSERT ON comments
        BEGIN
            SELECT CASE
                WHEN (SELECT id from books WHERE id=NEW.book) IS NULL
                THEN RAISE(ABORT, 'Foreign key violation: book not in books')
            END;
        END;
CREATE TRIGGER fkc_comments_update
        BEFORE UPDATE OF book ON comments
        BEGIN
            SELECT CASE
                WHEN (SELECT id from books WHERE id=NEW.book) IS NULL
                THEN RAISE(ABORT, 'Foreign key violation: book not in books')
            END;
        END;
