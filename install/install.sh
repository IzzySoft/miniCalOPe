#!/bin/bash
# Create a new database
#
# Simply run this script and copy the created (empty) database to where your
# config points to. Then you can run `php scan.php` anytime you want to refresh
# your database (e.g. when you added new books)

# remove database file if exists
if [ -e metadata.db ]; then
  rm -f metadata.db
fi

for file in *.sql; do
  sqlite3 metadata.db <$file
done
