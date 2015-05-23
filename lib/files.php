<?php
 #############################################################################
 # miniCalOPe                               (c) 2010-2015 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

require_once(dirname(__FILE__).'/common.php');
require_once(__DIR__.'/class.filefuncs.php');
$filefuncs = new filefuncs($logger,$use_markdown,$bookformats,$bookdesc_ext,$bookmeta_ext,$check_xml,$skip_broken_xml);

#==========================================================[ Cover extract ]===
/** Extract a specific file from an already open ZIP
 * @function extract_file
 * @param object zip ZIP resource returned by zip_open()
 * @param object zip_entry ZIP_ENTRY resource returned by zip_read
 * @param string target complete name of the file to create
 * @return boolean success
 * @brief currently only used by extract_cover
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
  $cover = $GLOBALS['filefuncs']->getCover($substr($file,0,strrpos($file,'.')));
  if ( !empty($cover) ) return; // cover already there

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