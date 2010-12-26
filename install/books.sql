--
-- Books
--
CREATE TABLE books ( id      INTEGER PRIMARY KEY AUTOINCREMENT,
                             title     TEXT NOT NULL DEFAULT 'Unknown' COLLATE NOCASE,
                             sort      TEXT COLLATE NOCASE,
                             timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                             uri       TEXT,
                             series_index INTEGER NOT NULL DEFAULT 1,
                             author_sort TEXT COLLATE NOCASE,
                             isbn TEXT DEFAULT "" COLLATE NOCASE,
                             path TEXT NOT NULL DEFAULT ""
                        );
CREATE INDEX authors_idx ON books (author_sort COLLATE NOCASE, sort COLLATE NOCASE);
CREATE INDEX books_idx ON books (sort COLLATE NOCASE);
CREATE INDEX series_sort_idx ON books (series_index, id);

CREATE TRIGGER books_delete_trg
        AFTER DELETE ON books
        BEGIN
            DELETE FROM books_authors_link WHERE book=OLD.id;
            DELETE FROM books_publishers_link WHERE book=OLD.id;
            DELETE FROM books_ratings_link WHERE book=OLD.id;
            DELETE FROM books_series_link WHERE book=OLD.id;
            DELETE FROM books_tags_link WHERE book=OLD.id;
            DELETE FROM data WHERE book=OLD.id;
            DELETE FROM comments WHERE book=OLD.id;
            DELETE FROM conversion_options WHERE book=OLD.id;
        END;
/*
CREATE TRIGGER books_insert_trg
        AFTER INSERT ON books
        BEGIN
          UPDATE books SET sort=title_sort(NEW.title) WHERE id=NEW.id;
        END;
CREATE TRIGGER books_update_trg
        AFTER UPDATE ON books
        BEGIN
          UPDATE books SET sort=title_sort(NEW.title) WHERE id=NEW.id;
        END;
*/