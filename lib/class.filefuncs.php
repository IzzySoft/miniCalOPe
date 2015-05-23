<?php
#############################################################################
# miniCalOPe                               (c) 2010-2015 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
#############################################################################

class filefuncs {
  /**
   * Dealing with files
   *
   * @package     Api
   * @author      Andreas Itzchak Rehberg
   * @brief       Depends on the logging class
   */

  /**
   * RegEx for cover images
   * @var protected string coverMask
   */
  protected $coverMask = '!\.(jpg|jpeg|png|gif)$!';

  /**
   * file extensions considered to belong to eBooks
   * @var protected array bookformats
   */
  protected $bookformats = array('epub','mobi','pdf');

  /**
   * files with these extensions are considered holding book descriptions
   * @var protected array bookdesc_ext
   */
  protected $bookdesc_ext = array('desc');

  /**
   * files with this extension hold Metadata for the corresponding book
   * @var protected string bookmeta_ext
   */
  protected $bookmeta_ext = 'data';

  /**
   * check for possible XML errors in description files and fix (if possible) plus log them?
   * @var protected bool checkXML
   */
  protected $checkXML = TRUE;

  /**
   * whether descriptions with broken XML should be discarded
   * @var protected bool skipBrokenXML
   */
  protected $skipBrokenXML = TRUE;

  /**
   * instance of the logging class
   * @var protected object logger
   */
  protected $logger;

  /**
   * instance of the Markdown parser (Markdown Extra)
   * @var protected parser
   */
  protected $parser;

  /**
   * RegEx for tags we shall not add linebreaks to in non-Markdown mode
   * @var protected nobrregs
   */
  protected $nobrregs;

  /**
   * the constructor
   * @param ref object logger Instance of the logger class to be used
   * @param bool markdown Whether Markdown is enabled at all
   * @param array bookformats File extensions of eBooks
   * @param array bookdesc_ext File extensions for eBook descriptions
   * @param string bookmeta_ext File extension for Metadata files
   * @param bool checkXML Whether to perfon XML check on book descriptions
   * @param bool skipBrokenXML Whether descriptions with broken XML should be discarded
   */
  public function __construct(&$logger,$markdown,$bookformats,$bookdesc_ext,$bookmeta_ext,$checkXML=TRUE,$skipBrokenXML=TRUE) {
    $this->logger = $logger;
    if ($markdown) {
      require_once('Michelf/MarkdownExtra.inc.php');
      $this->parser = new Michelf\MarkdownExtra();
    }
    $this->bookformats   = $bookformats;
    $this->bookdesc_ext  = $bookdesc_ext;
    $this->bookmeta_ext  = $bookmeta_ext;
    $this->checkXML      = $checkXML;
    $this->skipBrokenXML = $skipBrokenXML;

    // block-level elements in HTML – add no BR here (https://developer.mozilla.org/de/docs/Web/HTML/Block-level_elemente)
    $nobrtags = array('li','ol','ul','div','br','p','pre','blockquote','h\d','table','tr','th','td','hr','dd','dl','dt');
    $this->nobrregs = '';
    foreach($nobrtags as $x) $this->nobrregs .= "|\<\/$x\>|\<$x\>|\<$x\/\>";
    $this->nobrregs = substr($this->nobrregs,1);
    // end block-level definitions (used below on each description file)
  }

  /**
   * analyze contents of .data file
   * @param ref array data Where to store the result into
   * @param string filename (Path and) name of the .data file
   */
  public function readData(&$data,$filename) {
    $lines = explode( "\n", file_get_contents($filename) );
    $i = 0;
    foreach($lines as $line) {
      ++$i;
      $line = trim($line);
      if ( empty($line) ) continue;
      if ( substr($line,0,1) == '#' ) continue; // comment
      $tmp = explode('::',$line);
      $tmp[0] = strtolower($tmp[0]);
      if ( in_array($tmp[0],array('author','tag')) ) $data[$tmp[0]][] = $tmp[1];
      elseif ( in_array($tmp[0],array('series','series_index','rating','publisher','isbn','uri')) ) $data[$tmp[0]] = $tmp[1];
      elseif ( in_array($tmp[0],array('title','titel')) ) $data['title'] = $tmp[1];
      else $this->logger->notice("Cannot find keyword in line $i of file '$filename' [$line]",'SCAN');
    }
    if ( !empty($data['series_index']) && !is_numeric($data['series_index']) ) {
      $this->logger->warn("! series_index must be integer - got '".$data['series_index']."' in '$filename' - ignoring it",'SCAN');
      unset($data['series_index']);
      unset($data['series']);
    }
    if ( !empty($data['publisher']) && preg_match('@\&(?!amp;)@m',$data['publisher']) ) { // unescaped & cause trouble in XML
      $this->logger->debug("! unescaped & for publisher (".$data['publisher'].") in '$filename', trying to auto-fix",'SCAN');
      $data['publisher'] = preg_replace('@\&(?!amp;)@ms','&amp;',$data['publisher']);
    }
  }

  /**
   * Format book description
   * take care for formatting (e.g. line breaks)
   * @param ref string desc The description text
   * @param bool markdown Whether desc is to be considered Markdown fromatted
   */
  public function formatDesc(&$desc,$markdown) {
    if ($markdown) {
      $desc = $this->parser->transform($desc);
    } else {
      $desc = preg_replace('!(\r\n?|\n){2}!',"\n<br/>\n",$desc); // nl2br for empty lines
      $desc = preg_replace('/(?<!'.$this->nobrregs.')\s*([\n\r])/i',"<br/>\n",$desc); // nl2br except after block-level elements
    }
  }

  /**
   * Validate XML
   * This performs just a raw check for unmatched HTML tags and unencoded "&"
   * @param ref string xml Text to validate
   * @param string filename Which file name to report for logging
   */
  public function validateXML(&$xml, $filename) {
    // check for unmatched HTML tags. ATTENTION: this currently does NOT catch wrong nestings like "<b><i></b></i>" !!!
    $foo = preg_replace("!(<([\w]+)[^>]*/>)!ims",'',$xml); // simple <TAG/>s
    $foo = preg_replace("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/ims",'$3',preg_replace('![\n\r]!ms','',$foo));
    while ( preg_match_all("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/ims",$foo,$matches) ) $foo = preg_replace("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/ims",'$3',$foo); // nested?
    if ( strpos($foo,'<')!==FALSE ) {
      $this->logger->error("! Errors in '$filename': Unmatched HTML tags",'SCAN');
      if ($this->skipBrokenXML) $xml = '';
    }
    // Check for unencoded '&' and fix them up
    if ( preg_match('@\&(?!amp;)@m',$xml) ) {
      $this->logger->debug("! fixing unescaped & in '$filename'",'SCAN');
      $xml = preg_replace('@\&(?!amp;)@ms','&amp;',$xml);
    }
    return;
    // check for unencoded '&' in HREFs -- obsoleted by previous step?
    preg_match_all('!href=(["\'])([^\1]+?)\1!ims',$xml,$matches);
    foreach ($matches[2] as $match) if ( preg_match('@\&(?!amp;)@',$match) ) {
      $this->logger->error("! Errors in '$filename': unencoded '&' in URL",'SCAN');
    }
  }

  /**
   * Check if we already have a cover image and return its name
   * @param string bookpath full path and name to the ebook file w/o file ext
   * @return filename path and name of the cover image file
   * @brief used by scan and web to make sure both ends share the same file mask
   */
  function getCover($bookpath) {
    $covers = glob($bookpath."\.*"); $coverimg = '';
    foreach ($covers as $c) if ( preg_match($this->coverMask,$c) ) {
      $coverimg = $c; break;
    }
    return $coverimg;
  }

  /**
   * Resize cover image to specified max width (unless it's already smaller)
   * @param string img Path-and-name of the image file
   * @param int maxwid Max allowed width for the "thumbnail"
   * @return bool success
   */
  public function resizeCover($img, $maxwid) {
    if ( !function_exists('imagecreatefromjpeg') ) {
      $this->logger->error("No support for PHP graphics library (GD) found, cannot resize images.",'SCAN');
      return FALSE;
    }
    if ( !file_exists($img) ) {
      $this->logger->error("Cannot resize non-existing image '${img}'",'SCAN');
      return FALSE;
    }
    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($img);
    if ( $source_image_width <= $maxwid ) return TRUE; // is already fine
    switch ($source_image_type) {
      case IMAGETYPE_GIF:
        $source_gd_image = imagecreatefromgif($img);
        break;
      case IMAGETYPE_JPEG:
        $source_gd_image = imagecreatefromjpeg($img);
        break;
      case IMAGETYPE_PNG:
        $source_gd_image = imagecreatefrompng($img);
        break;
    }
    if ($source_gd_image === false) {
      $this->logger->error("Could not initialize GD for '${img}'",'SCAN');
      return FALSE;
    }
    $new_height = (int) $maxwid * $source_image_height / $source_image_width;
    $this->logger->info("    - resizing '${img}' to ${maxwid}x${new_height}",'SCAN');
    $thumb_gd_image = imagecreatetruecolor($maxwid,$new_height);
    imagecopyresampled($thumb_gd_image, $source_gd_image, 0, 0, 0, 0, $maxwid,$new_height, $source_image_width, $source_image_height);
    switch ($source_image_type) {
      case IMAGETYPE_GIF:
        $rc = imagegif($thumb_gd_image, $img);
        break;
      case IMAGETYPE_JPEG:
        $rc = imagejpeg($thumb_gd_image, $img, 75);
        break;
      case IMAGETYPE_PNG:
        $rc = imagepng($thumb_gd_image, $img, 9);
        break;
    }
    imagedestroy($source_gd_image);
    imagedestroy($thumb_gd_image);
    if ( !$rc ) $this->logger->error("Could not write resized image '${img}'",'SCAN');
    return $rc;
  }

  /**
   * Scan a folder in the book location for either sub-directories or book files
   * @param string dirname directory to scan
   * @param optional string mode scan for 'dirs' (default) or 'files'
   * @param optional int markdown Whether to interprete .desc files as Markdown (1) or not (0, default). Only relevant when mode='files'.
   * @return array list mode=dirs: array of dirnames (1-level); else: array[name] with [files][ext], [desc], [lastmod] (unixtime), [title], [fbasename]
   *               plus optionally arrays [author] + [tag], strings [rating], [series], [series_index], [publisher], [isbn], [uri]
   */
  function scanFolder($dirname,$mode='dirs',$markdown=0) {
    $dir = dir("$dirname");
    $list = array();
    while ( $file=$dir->read() ) {
      if ( substr($file,0,1) == '.' ) continue; // ignore ".", "..", and ".hidden files"
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
        $bnam = substr($file,0,$pos);
        $nam = "$dirname/$bnam";
        $list[$nam]['fbasename'] = $bnam;
        if ( in_array($ext,$this->bookformats) ) $list[$nam]['files'][$ext] = $fullname;
        elseif ( in_array($ext,$this->bookdesc_ext) ) {
          $list[$nam]['desc'] = trim(file_get_contents($fullname));
          $this->formatDesc($list[$nam]['desc'],$markdown);
          if ( $this->checkXML && !empty($list[$nam]['desc']) ) $this->validateXML($list[$nam]['desc'], $fullname);
        } elseif ( $ext == $this->bookmeta_ext ) {
          $this->readData($list[$nam],$fullname);
        }

        $lastmod = filemtime($fullname);
        if ( empty($list[$nam]['lastmod']) || $list[$nam]['lastmod'] < $lastmod ) $list[$nam]['lastmod'] = $lastmod;
        if ( empty($list[$nam]['title']) ) $list[$nam]['title'] = $bnam;
        continue;
      }

    }
    if ($mode=='dirs') sort($list);
    return $list;
  }


  // ======================================================[ Calibre stuff ]===
  // miniCalOPe doesn't work with recent Calibre versions, but we might fix that
  // later – so we keep this

  /**
   * Purge all files in the specified directory
   * @param string path
   * @brief only used by prepare_covers, so Calibre stuff only
   */
  function purgeDir($path) {
    if ( !is_dir($path) ) {
      $this->logger->error("! Cannot purge '$path' - directory does not exist!",'SCAN');
      return;
    }
    $this->logger->info("* Purging directory '$path'",'SCAN');
    $dir = dir($path);
    while ( $file=$dir->read() ) {
      if ( in_array($file,array('.','..')) ) continue;
      $fullname = $path . DIRECTORY_SEPARATOR . $file;
      $this->logger->debug("  + removing '$file'",'SCAN');
      if ( !unlink($fullname) ) $this->logger->error("! Could not delete '$file'",'SCAN');
    }
  }

  /**
   * Checking for cover images and linking them to the cover dir
   * @param string coverdir location to create the symlinks in
   * @param string bookroot Root to where the ebooks reside
   * @param string coverMode cover_mode of the current configuration
   * @param string dbfile Path and name to Calibre's SQLite database file
   * @brief for Calibre only
   * @version Calibre support is not working for current versions due to changes in their db struct
   */
  function prepareCalibreCovers($coverdir,$bookroot,$coverMode,$dbfile) {
    $this->purgeDir($coverdir);
    $this->logger->info('* Linking cover images','SCAN');
    $db = new db($dbfile);
    $db->query('SELECT id,path FROM books');
    while ( $db->next_record() ) {
      $path = $bookroot.$db->f('path');
      $id   = $db->f('id');
      switch($coverMode) {
        case 'calibre':
          $coverimg  = $path.DIRECTORY_SEPARATOR.'cover.jpg';
          $coverlink = $coverdir.DIRECTORY_SEPARATOR.$id.'.jpg';
          if ( file_exists($coverimg) ) {
            $this->logger->debug("  - $coverimg -> $coverlink",'SCAN');
            symlink($coverimg,$coverlink);
          } else {
            $this->logger->debug("  - No cover for ID '$id' at '$path'",'SCAN');
          }
          break;
        case 'simple':
        default: $this->logger->debug('Cover-Link support disabled.','SCAN');
      }
    }
  }

}

?>