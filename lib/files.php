<?php
 #############################################################################
 # miniCalOPe                               (c) 2010-2011 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 # ------------------------------------------------------------------------- #
 # Template class taken from PHPLIB                                          #
 # (C) Copyright 1999-2000 NetUSE GmbH                                       #
 #                    Kristian Koehntopp                                     #
 #############################################################################

require_once(dirname(__FILE__).'/common.php');

/** Scan a folder in the book location for either sub-directories or book files
 * @function scanFolder
 * @param string dirname directory to scan
 * @param optional string mode scan for 'dirs' (default) or 'files'
 * @return array list mode=dirs: array of dirnames (1-level); else: array[name] with [files][ext], [desc], [lastmod] (unixtime)
 *               plus optionally arrays [author] + [tag], strings [rating], [series], [series_index], [publisher], [isbn], [uri]
 */
function scanFolder($dirname,$mode='dirs') {
    GLOBAL $bookformats, $bookdesc_ext;
    $dir = dir("$dirname");
    $list = array();
    while ( $file=$dir->read() ) {
      if ( in_array($file,array('.','..')) ) continue;
      $fullname = $dirname . DIRECTORY_SEPARATOR . $file;

      // directories
      if ( $mode=='dirs' && is_dir($fullname) ) {
        $list[] = $file;
        continue;
      }

      // files
      if ( $mode=='files' && is_file($fullname) ) {
        $pos = strrpos($file,".");
        $ext = substr($file,$pos+1);
        $nam = substr($file,0,$pos);
        if ( in_array($ext,$bookformats) ) $list[$nam]['files'][$ext] = $fullname;
        elseif ( in_array($ext,$bookdesc_ext) ) $list[$nam]['desc'] = file_get_contents($fullname);
        elseif ( $ext == $GLOBALS['bookmeta_ext'] ) {
          $lines = explode( "\n", file_get_contents($fullname) );
          $i = 0;
          foreach($lines as $line) {
            ++$i;
            $line = trim($line);
            if ( empty($line) ) continue;
            if ( substr($line,0,1) == '#' ) continue; // comment
            $tmp = explode('::',$line);
            $tmp[0] = strtolower($tmp[0]);
            if ( in_array($tmp[0],array('author','tag')) ) $list[$nam][$tmp[0]][] = $tmp[1];
            elseif ( in_array($tmp[0],array('series','series_index','rating','publisher','isbn','uri')) ) $list[$nam][$tmp[0]] = $tmp[1];
            else $GLOBALS['logger']->notice("Cannot find keyword in line $i of file '$fullname' [$line]",'SCAN');
          }
          if ( !empty($list[$nam]['series_index']) && !is_numeric($list[$nam]['series_index']) ) {
            $GLOBALS['logger']->warn("! series_index must be integer - got '".$list[$nam]['series_index']."' in '$fullname' - ignoring it",'SCAN');
            unset($list[$nam]['series_index']);
          }
        }
        $lastmod = filemtime($fullname);
        if ( empty($list[$nam]['lastmod']) || $list[$nam]['lastmod'] < $lastmod ) $list[$nam]['lastmod'] = $lastmod;
        continue;
      }

    }
    return $list;
}

/** Purge all files in the specified directory
 * @function purge_dir
 * @param string path
 */
function purge_dir($path) {
    if ( !is_dir($path) ) {
        $GLOBALS['logger']->error("! Cannot purge '$path' - directory does not exist!",'SCAN');
        return;
    }
    $GLOBALS['logger']->info("* Purging directory '$path'",'SCAN');
    $dir = dir($path);
    while ( $file=$dir->read() ) {
      if ( in_array($file,array('.','..')) ) continue;
      $fullname = $path . DIRECTORY_SEPARATOR . $file;
      $GLOBALS['logger']->debug("  + removing '$file'",'SCAN');
      unlink($fullname);
    }
}

/** Checking for cover images and linking them to the cover dir
 * @function prepare_covers
 * @param string coverdir location to create the symlinks in
 */
function prepare_covers($coverdir) {
    purge_dir($coverdir);
    $GLOBALS['logger']->info('* Linking cover images','SCAN');
    $db = new db($GLOBALS['dbfile']);
    $db->query('SELECT id,path FROM books');
    while ( $db->next_record() ) {
      $path = $GLOBALS['bookroot'].$db->f('path');
      $id   = $db->f('id');
      switch($GLOBALS['cover_mode']) {
        case 'calibre':
          $coverimg  = $path.DIRECTORY_SEPARATOR.'cover.jpg';
          $coverlink = $coverdir.DIRECTORY_SEPARATOR.$id.'.jpg';
          if ( file_exists($coverimg) ) {
              $GLOBALS['logger']->debug("  - $coverimg -> $coverlink",'SCAN');
              symlink($coverimg,$coverlink);
          } else {
              $GLOBALS['logger']->debug("  - No cover for ID '$id' at '$path'",'SCAN');
          }
          break;
        case 'simple':
        default: $GLOBALS['logger']->debug('Cover support disabled.','SCAN');
      }
    }
}

#==========================================================[ Cover extract ]===
/** Extract a specific file from an already open ZIP
 * @function extract_file
 * @param object zip ZIP resource returned by zip_open()
 * @param object zip_entry ZIP_ENTRY resource returned by zip_read
 * @param string target complete name of the file to create
 * @return boolean success
 */
function extract_file($zip,$zip_entry,$target) {
  $success = TRUE;
  if (zip_entry_open($zip, $zip_entry, "r")) {
    if ($fd = @fopen($target, 'wb')) {
      fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
      fclose($fd);
    } else {
      $GLOBALS['logger']->error('! Could not open ZIP target "$target"','SCAN');
      $success = FALSE;
    }
    zip_entry_close($zip_entry);
  } else {
    $success = FALSE;
  }
  return $success;
}

/** Extract cover file from *.epub
 * @function extract_cover
 * @param string file complete name of the *.epub file
 */
function extract_cover($file) {
  if ( !file_exists($file) ) return; // no input
  $cover = substr($file,0,strrpos($file,'.')).'.jpg'; // name of the target cover image file
  if ( file_exists($cover) ) return; // already there
  if ( file_exists(preg_replace('!jpg$!','png',$cover)) ) return; // already there

  $zip = zip_open($file);
  if (!$zip || is_int($zip)) {
    $GLOBALS['logger']->error("! Could not ZIPopen '$file'",'SCAN');
    return;
  }

  $zipok = TRUE;
  while ($zip_entry = zip_read($zip)) { // walk the archive contents
    if ( zip_entry_name($zip_entry) == 'content/resources/_cover_.jpg' ) { // Calibre cover
      $zipok = extract_file($zip,$zip_entry,$cover);
      break;
    } elseif ( preg_match('!\bcover\.(png|jpg|jpeg)$!i',zip_entry_name($zip_entry),$match) ) { // feedbooks & co
      if ( $match[1]!='jpeg' ) $cover = preg_replace('!jpg$!',$match[1],$cover);
      $zipok = extract_file($zip,$zip_entry,$cover);
      break;
    } elseif ( preg_match('!cover-image.jpg$!i',zip_entry_name($zip_entry),$match) ) { // some on Archive.ORG
      $zipok = extract_file($zip,$zip_entry,$cover);
      break;
    } elseif ( preg_match('!cover1.jpeg$!i',zip_entry_name($zip_entry),$match) ) {
      $zipok = extract_file($zip,$zip_entry,$cover);
      break;
    }
  }
  if ( !$zipok ) $GLOBALS['logger']->error('! Failed to extract cover from "$file"','SCAN');

  zip_close($zip);
}

?>