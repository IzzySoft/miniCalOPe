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
 * @return array list mode=dirs: array of dirnames (1-level); else: array[name] with [files][ext] and [desc]
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

?>