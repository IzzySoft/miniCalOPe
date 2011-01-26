--
-- ratings
--
CREATE TABLE ratings ( id   INTEGER PRIMARY KEY,
                               rating INTEGER CHECK(rating > -1 AND rating < 6),
                               UNIQUE (rating)
                             );
INSERT INTO ratings (id,rating) VALUES (1,1);
INSERT INTO ratings (id,rating) VALUES (2,2);
INSERT INTO ratings (id,rating) VALUES (3,3);
INSERT INTO ratings (id,rating) VALUES (4,4);
INSERT INTO ratings (id,rating) VALUES (5,5);
