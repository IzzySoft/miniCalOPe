<?php
#############################################################################
# miniCalOPe                               (c) 2010-2015 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
# ------------------------------------------------------------------------- #
# Scan for books and feed database                                          #
#############################################################################
# $Id$

require_once('./lib/class.logging.php'); // must come first as it also defines some CONST
require_once('./config.php');
if ( $scan_cli_only && php_sapi_name() != 'cli' ) { // protect agains access by visitors
  header('HTTP/1.0 403 Forbidden');
  echo "<h1>You are not allowed to be here.</h1>\n<p>The gatekeeper won't let you see this page. Please point your browser to a different one.</p>\n";
  trigger_error('Web access to scan scripts denied by config', E_USER_ERROR);
  exit;
}
require_once('./lib/common.php');
require_once('./lib/class.filefuncs.php');
$filefuncs = new filefuncs($logger,$use_markdown,$bookformats,$bookdesc_ext,$bookmeta_ext,$check_xml,$skip_broken_xml);
require_once('./lib/db_sqlite3.php');
require_once('./lib/class.db.php');
if ( $autoExtract ) {
  require_once('./lib/class.epubdesc.php');
  if ( in_array('all',$extract2desc) || in_array('desc',$extract2desc) ) { // for trans of terms in bookdesc
    require_once('./lib/class.csv.php');
    $csv = new csv(";",'"',TRUE,FALSE);
  } else {
    $csv = NULL;
  }
} else {
  $csv = NULL;
}

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
$langs = $filefuncs->scanFolder($bookroot);

// Now collect the genres
foreach($langs as $lang) {
    GLOBAL $use_markdown;
    if ( !empty($uselangs) && !in_array($lang,$uselangs) ) {
        $logger->debug("* Skipping langDir '$lang'",'SCAN');
        continue;
    }
    $logger->info("* Scanning langDir '$lang'",'SCAN');
    if ( $csv !== NULL && file_exists("./lang/ebookterms.${lang}") ) {
      $csv = new csv(";",'"',TRUE,FALSE);
      $logger->debug('# Loaded terms for ebook desc from '.__DIR__."./lang/ebookterms.${lang}",'SCAN');
      $csv->import("./lang/ebookterms.${lang}");
    }
    $genres[$lang] = $filefuncs->scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang);

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
        $tauthors = $filefuncs->scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir);

        // Guess what - they wrote books!
        foreach($tauthors as $aidx => $author) {
            $logger->debug("    - Scanning author dir '$author'",'SCAN');
            $adir = $author;
            if ( in_array('author',$dotname_overrides) && file_exists($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir . DIRECTORY_SEPARATOR . '.name') ) {
              $author = trim(file_get_contents($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir . DIRECTORY_SEPARATOR . '.name'));
              $tauthors[$aidx] = $author;
            }
            if ( $gmarkdown && !file_exists($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir . DIRECTORY_SEPARATOR . '.nomarkdown') ) {
              $tbooks = $filefuncs->scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir, 'files', $gmarkdown);
            } else {
              $tbooks = $filefuncs->scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $gdir . DIRECTORY_SEPARATOR . $adir, 'files', 0);
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
              if ( $GLOBALS['autoExtract'] && isset($tbooks[$book]['files']['epub']) ) {
                $epub = new epubdesc($tbooks[$book]['files']['epub']);
                $pathinfo = pathinfo($tbooks[$book]['files']['epub']);
                $cover = $filefuncs->getCover($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename']);
                if ( $extractCover > 0 && $GLOBALS['cover_mode']!='off' && empty($cover) ) {
                  if ( $rc = $epub->writeCover($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename']) ) {
                    $cover = $filefuncs->getCover($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename']);
                    $logger->info("    - extracted cover: '${cover}'",'SCAN');
                  }
                  if ( $extractCover > 1 && $rc ) {
                    $filefuncs->resizeCover($cover,$cover_width);
                  }
                }
                if ( !empty($extract2data) && !file_exists($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookmeta_ext) ) {
                  $logger->info("    - extracting Metadata: '".$pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookmeta_ext."'",'SCAN');
                  $epub->setExtract2data($extract2data);
                  $epub->setDataExt($bookmeta_ext);
                  $epub->writeData($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename']);
                  if ( file_exists($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookmeta_ext) )
                    $filefuncs->readData($tbooks[$book],$pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookmeta_ext);
                }
                if ( !empty($extract2desc) && !$filefuncs->file_exists_glob($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'],$bookdesc_ext) ) {
                  $logger->info("    - extracting Descdata: '".$pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookdesc_ext[0]."'",'SCAN');
                  if ( !empty($csv->data) ) $epub->setTerms($csv->data);
                  $epub->setExtract2desc($extract2desc);
                  $epub->setDescExt($bookdesc_ext[0]);
                  $epub->writeDesc($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename']);
                  if ( file_exists($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookdesc_ext[0]) ) {
                    $tbooks[$book]['desc'] = trim(file_get_contents($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookdesc_ext[0]));
                    $filefuncs->formatDesc($tbooks[$book]['desc'],$gmarkdown);
                    if ( $check_xml && !empty($tbooks[$book]['desc']) ) $filefuncs->validateXML($tbooks[$book]['desc'], $pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['filename'].'.'.$bookdesc_ext[0]);
                  }
                }
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

$logger->info('* Cleaning up database','SCAN');
$db->query('VACUUM');
$db->query('REINDEX');

$logger->info("* Done",'SCAN');

exit;

?>