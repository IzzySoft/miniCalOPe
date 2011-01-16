<?php
 #############################################################################
 # miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
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

/**
 * @function scanDir
 * @param string dirname directory to scan
 * @param optional string mode scan for 'dirs' (default) or 'files'
 * @return array list mode=dirs: array of dirnames (1-level); else: array[name] with [files][ext], [desc] and [lastmod] (unixtime)
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
        debugOut("! Cannot purge '$path' - directory does not exist!");
        return;
    }
    debugOut("* Purging directory '$path'");
    $dir = dir($path);
    while ( $file=$dir->read() ) {
      if ( in_array($file,array('.','..')) ) continue;
      $fullname = $path . DIRECTORY_SEPARATOR . $file;
      debugOut("  + removing '$file'");
      unlink($fullname);
    }
}

/** Checking for cover images and linking them to the cover dir
 * @function prepare_covers
 * @param string coverdir location to create the symlinks in
 */
function prepare_covers($coverdir) {
    purge_dir($coverdir);
    debugOut('* Linking cover images');
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
              debugOut("  - $coverimg -> $coverlink");
              symlink($coverimg,$coverlink);
          }
          else debugOut("  - No cover for ID '$id' at '$path'");
          break;
        case 'simple':
        default: debugOut('Cover support disabled.');
      }
    }
}

#==========================================================[ Cover extract ]===
/** Extract a specific file from an already open ZIP
 * @function extract_file
 * @param object zip ZIP resource returned by zip_open()
 * @param object zip_entry ZIP_ENTRY resource returned by zip_read
 * @param string target complete name of the file to create
 */
function extract_file($zip,$zip_entry,$target) {
  if (zip_entry_open($zip, $zip_entry, "r")) {
    if ($fd = @fopen($target, 'wb')) {
      fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
      fclose($fd);
    }
    zip_entry_close($zip_entry);
  }
}

/** Extract cover file from *.epub
 * @function extract_cover
 * @param string file complete name of the *.epub file
 */
function extract_cover($file) {
  if ( !file_exists($file) ) return; // no input
  $cover = substr($file,0,strrpos($file,'.')).'.jpg'; // name of the target cover image file
  if ( file_exists($cover) ) return; // already there

  $zip = zip_open($file);
  if (!$zip || is_int($zip)) return;

  while ($zip_entry = zip_read($zip)) { // walk the archive contents
    if ( zip_entry_name($zip_entry) == 'content/resources/_cover_.jpg' ) { // Calibre cover
      extract_file($zip,$zip_entry,$cover);
      break;
    } elseif ( preg_match('!\bcover\.(png|jpg)$!i',zip_entry_name($zip_entry),$match) ) { // feedbooks & co
      $cover = preg_replace('!jpg$!',$match[1],$cover);
      extract_file($zip,$zip_entry,$cover);
      break;
    }
  }

  zip_close($zip);
}

#================================================================[ Logging ]===
/** Check for Logfile, create if it does not exist
 * @function check_logfile
 * @return boolean success TRUE if OK, FALSE otherwise, which means no logging possible.
 */
function check_logfile() {
    if ( isset($GLOBALS['logfile']) ) $logfile = $GLOBALS['logfile'];
    else return FALSE;
    if ( empty($logfile) ) return FALSE;
    if ( is_writeable($logfile) ) return TRUE;
    if ( file_exists($logfile) ) return FALSE; // file exists, but we cannot write into it
    if ( is_dir(dirname($logfile)) && is_writeable(dirname($logfile)) ) return TRUE; // fresh file
}

/** Create log entry
 * @function logg
 * @param string msg Message to write to log
 * @param optional string module Module responsible (Default: 'NONE')
 * @param optional string level Log Level (Default: 'INFO')
 */
function logg($msg,$module='NONE',$level='INFO') {
    if ( !check_logfile() ) return; // if we have no log, there's nothing to do
    // log format: YYYYMMDD HH:MM:SS <who-was-it> <level> <modul> <message>
    if ( isset($_SERVER) ) $who = $_SERVER['REMOTE_ADDR'];
    else $who = 'local';
    error_log(date('Y-m-d H:i:s')." $who $level $module $msg\n", 3, $GLOBALS['logfile']);
}

?>