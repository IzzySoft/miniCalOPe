<?
#############################################################################
# miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
# ------------------------------------------------------------------------- #
# Scan for books and feed database                                          #
#############################################################################
# $Id$

require_once('./config.php');
require_once('./lib/common.php');
require_once('./lib/files.php');
require_once('./lib/db_sqlite3.php');
require_once('./lib/db.php');
require_once('./lib/template.php');

$tpl = new Template('tpl');
$db = new db($dbfile);

$pubdate = date('c');
$books = array();
$genres = array(); $allGenres = array();
$authors = array();

// directory structure for books directory:
// <lang>/<genre>/<author>/<books>

#===========================================================[ Collect data ]===
// Go for the languages available
debugOut("Scanning $bookroot");
debugOut("use_lang: $use_lang");
debugOut("Languages: ".implode(', ',$uselangs));
debugOut("DBFile: $dbfile");
$langs = scanFolder($bookroot);

// Now collect the genres
foreach($langs as $lang) {
    if ( !empty($uselangs) && !in_array($lang,$uselangs) ) {
        debugOut("* Skipping langDir '$lang'");
        continue;
    }
    debugOut("* Scanning langDir '$lang'");
    $genres[$lang] = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang);
    $allGenres     = array_merge($allGenres,$genres[$lang]);

    // Now come the authors
    foreach($genres[$lang] as $genre) {
        debugOut("  + Scanning genre '$genre'");
        $tauthors = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $genre);
        $authors = array_merge( $authors, $tauthors);

        // Guess what - they wrote books!
        foreach($tauthors as $author) {
            debugOut("    - Scanning author '$author'");
            $tbooks = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $genre . DIRECTORY_SEPARATOR . $author, 'files');
            //array[name] with [files][ext] and [desc]
            foreach($tbooks as $book=>$dummy) {
              $tbooks[$book]['lang']   = $lang;
              $tbooks[$book]['genre']  = $genre;
              $tbooks[$book]['author'] = $author;
              if ( isset($tbooks[$book]['files']['epub']) && $GLOBALS['cover_mode']!='off' ) extract_cover($tbooks[$book]['files']['epub']);
            }
            $books = array_merge($books,$tbooks);
        }
    }
}
sort($allGenres);
$allGenres = array_unique($allGenres);
$authors = array_unique($authors);

#======================================================[ Feed the database ]===
debugOut('* Updating database');
debugOut('  + Truncating');
$db->truncAll();
debugOut('  + Inserting Tags');
$db->make_genres($allGenres);
debugOut('  + Inserting Authors');
$db->make_authors($authors);
debugOut('  + Inserting Books');
$db->make_books($books);

debugout("Processed:\n- ".count($authors)." authors\n- ".count($books)." books");

exit;

?>