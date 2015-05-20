<?php
#############################################################################
# miniCalOPe                               (c) 2010-2011 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
# ------------------------------------------------------------------------- #
# Scan for books and feed database                                          #
#############################################################################
# $Id$

require_once('./lib/logging.php'); // must come first as it also defines some CONST
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
$series = array();
$publisher = array();

// directory structure for books directory:
// <lang>/<genre>/<author>/<books>

#===========================================================[ Collect data ]===
// Go for the languages available
$logger->info("Scanning $bookroot [MODE=$scan_dbmode]",'SCAN');
$logger->debug("use_lang: $use_lang",'SCAN');
$logger->debug("Languages: ".implode(', ',$uselangs),'SCAN');
$logger->debug("DBFile: $dbfile",'SCAN');
$langs = scanFolder($bookroot);

// Now collect the genres
foreach($langs as $lang) {
    GLOBAL $use_markdown;
    if ( !empty($uselangs) && !in_array($lang,$uselangs) ) {
        $logger->debug("* Skipping langDir '$lang'",'SCAN');
        continue;
    }
    $logger->info("* Scanning langDir '$lang'",'SCAN');
    $genres[$lang] = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang);

    // Now come the authors
    foreach($genres[$lang] as $gidx => $genre) {
        $logger->info("  + Scanning genre dir '$genre'",'SCAN');
        $gdir = $genre;
        if ( $use_markdown && !file_exists($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $genre . DIRECTORY_SEPARATOR . '.nomarkdown') ) $gmarkdown = 1;
        else $gmarkdown = 0;
        if ( in_array('genre',$dotname_overrides) && file_exists($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $genre . DIRECTORY_SEPARATOR . '.name') ) {
          $genre = trim(file_get_contents($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $genre . DIRECTORY_SEPARATOR . '.name'));
          $genres[$lang][$gidx] = $genre;
        }
        $allGenres = array_merge($allGenres,array($genre));
        $tauthors = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir);

        // Guess what - they wrote books!
        foreach($tauthors as $aidx => $author) {
            $logger->debug("    - Scanning author dir '$author'",'SCAN');
            $adir = $author;
            if ( in_array('author',$dotname_overrides) && file_exists($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir . DIRECTORY_SEPARATOR . '.name') ) {
              $author = trim(file_get_contents($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir . DIRECTORY_SEPARATOR . '.name'));
              $tauthors[$aidx] = $author;
            }
            if ( $gmarkdown && !file_exists($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir . DIRECTORY_SEPARATOR . '.nomarkdown') ) {
              $tbooks = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir, 'files', $gmarkdown);
            } else {
              $tbooks = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir, 'files', 0);
            }
            //array[name] with [files][ext], [desc] ([series],[series_index],[rating],[publisher],[isbn], [author],[tag]
            foreach($tbooks as $book=>$dummy) {
              $tbooks[$book]['lang']   = $lang;
              $tbooks[$book]['genre']  = $genre;
              if ( empty($tbooks[$book]['author']) ) { // no author defined in .data
                $authors = array_merge($authors,array($author));
                $tbooks[$book]['author'][] = $author;
              } else {
                $authors = array_merge($authors,$tbooks[$book]['author']);
                if ( !in_array('author',$data_overrides) ) { // in no-override mode, merge in author from dirname
                  $authors = array_merge($authors,$author);
                  if ( !(is_array($tbooks[$book]['author']) && in_array($author,$tbooks[$book]['author'])) ) $tbooks[$book]['author'][] = $author;
                }
              }
              if ( $GLOBALS['autoExtract'] ) {
                if ( $GLOBALS['extractCover'] > 0 && isset($tbooks[$book]['files']['epub']) && $GLOBALS['cover_mode']!='off' ) extract_cover($tbooks[$book]['files']['epub']);
              }
              if ( !empty($tbooks[$book]['tag']) ) $allGenres = array_merge($allGenres,$tbooks[$book]['tag']);   // from *.data file
              if ( !empty($tbooks[$book]['series']) ) $series[] = $tbooks[$book]['series'];                      // from *.data file
              if ( !empty($tbooks[$book]['publisher']) ) $publisher[] = $tbooks[$book]['publisher'];             // from *.data file
            }
            $books = array_merge($books,$tbooks);
        }
    }
}
sort($allGenres);

#======================================================[ Feed the database ]===
$logger->info('* Updating database','SCAN');
$db->truncAll();
$db->make_genres($allGenres);
if (!empty($publisher)) $db->make_publisher($publisher);
if (!empty($series)) $db->make_series($series);
$db->make_authors($authors);
$db->make_books($books);

$logger->info("* Done",'SCAN');

exit;

?>