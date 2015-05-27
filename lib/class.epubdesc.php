<?php
#############################################################################
# miniCalOPe                               (c) 2010-2015 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
#############################################################################

require_once(__DIR__.'/class.epub.php');

class epubdesc extends epub {
  /**
   * ePub class to extract metadata to .desc, .data and cover-image files
   *
   * @package     Api
   * @extends     epub
   * @author      Andreas Itzchak Rehberg
   * @license     GPL
   */

    /**
     * Type of text output for description. One of 'md' (Markdown) or 'html'.
     * @protected str type
     */
    protected $type;

    /**
     * Holds the description. Obtain via getDesc().
     * @protected str desc
     */
    protected $desc = NULL;

    /**
     * Holds the .data content. Obtain with getData().
     * @protected str data
     */
    protected $data = NULL;

    /**
     * How many TOC levels to include? 0 = no TOC at all
     * @protected int tocLevels default 2
     * @see setTOCLevels()
     */
    protected $tocLevels = 2;

    /**
     * Which terms to extract to .data files. Default: 'all'
     * @protected array extract2data
     * @see setExtract2data()
     */
    protected $extract2data = array('all');

    /**
     * File extension for the Metadata files. Default: 'data'
     * @protected string dataExt
     * @see setDataExt()
     */
    protected $dataExt = 'data';

    /**
     * What to extract to .desc files. Default: 'all'
     * @protected array extract2desc
     * @see setExtract2desc()
     */
    protected $extract2desc = array('all');

    /**
     * File extension for the Description files. Default: 'desc'
     * @protected string descExt
     * @see setDescExt()
     */
    protected $descExt = 'desc';

    /**
     * Our "dictionary" for text output. For setting up yours, see setDict()
     * @protected array terms
     */
    protected $terms = array(
      'author' => 'Autor',
      'bookproducer' => 'eBook Ersteller',
      'collaborator' => 'Mitarbeiter',
      'content' => 'Inhalt',
      'copyright' => 'Copyright',
      'date' => 'Datum',
      'editor' => 'Bearbeitung',
      'ebookPublished' => 'eBook veröffentlicht',
      'ebookCreated' => 'eBook erstellt',
      'firstpublished' => 'Erstveröffentlichung',
      'illustrator' => 'Illustrator',
      'modification' => 'Änderung',
      'published' => 'Veröffentlicht',
      'publisher' => 'Herausgeber',
      'redactor' => 'Redakteur',
      'source' => 'Quelle',
      'topic' => 'Thema',
      'translator' => 'Übersetzer'
    );

    /**
     * Constructor
     * @param ebookDir the *.epub file to analyze
     */
    public function __construct($ebookDir,$type='md',$lang='de') {
      parent::__construct($ebookDir);
      $type = strtolower($type);
      if ( !in_array($type,array('md','html')) ) $type = 'md'; // safeguard
      $this->type = $type;
    }

    /**
     * Set how much of the TOC should be included with the description
     * @param int levels 0..3 (0 to not include TOC at all)
     */
    public function setTOCLevels($levels) {
      if ( is_numeric($levels) && $levels < 4 ) $this->tocLevels = $levels;
      else {
        trigger_error('TOC level needs to be 0..3 (given: '.$levels.')',E_USER_WARNING);
      }
    }

    /**
     * Which terms to extract to .data files. Default: 'all'
     * @param array values
     * @brief valid values are either 'all' (to extract everything), or one or a
     *        combination of author,isbn,publisher,rating,series,series_index,tag,title,uri
     *        note that while 'isbn' and 'uri' are safe to use, there might be
     *        issues with the others. For details, see issue #4 at Github:
     *        https://github.com/IzzySoft/miniCalOPe/issues/4
     */
    public function setExtract2data($values) {
      $this->extract2data = array(); // reset first
      foreach ($values as $value) $this->extract2data[] = strtolower($value);
    }

    /**
     * Set the file extension for the Metadata files. Default (if this method is
     * not called): 'data'
     * @param str ext File extension
     */
    public function setDataExt($ext) {
      $this->dataExt = $ext;
    }

    /**
     * What to extract to .desc files. Default: 'all'
     * @param array values
     * @brief valid values are 'all' (to extract everything) or 'desc'
     *        (for "description only"). In future versions, we might diverse more.
     */
    public function setExtract2desc($values) {
      $this->extract2desc = array(); // reset first
      foreach ($values as $value) $this->extract2desc[] = strtolower($value);
    }

    /**
     * Set the file extension for the Description files. Default (if this method is
     * not called): 'desc'
     * @param str ext File extension
     */
    public function setDescExt($ext) {
      $this->descExt = $ext;
    }

    /**
     * language specific adjustments to our "dictionary"
     * @param array terms Array[0..n] or array[term,trans]
     * @brief this array doesn't have to be complete; we overwrite only those pairs specified
     */
    public function setTerms($terms) {
      foreach ($terms as $term) {
        $this->terms[$term['term']] = $term['trans'];
      }
    }

    /**
     * Extract a sincle file from the eBook
     * @param str zipfile filename to extract from (the *.epub)
     * @param str zip_entry name of the file to extract (with path from tze zip root, if any)
     * @param str target full path and file name of where the extracted file should be placed
     * @return bool success
     */
    function extract_file($zipfile,$zip_entry,$target) {
      if ( substr($zipfile,0,1) != '/' ) $zipfile = './'.$zipfile;
      return file_put_contents($target,file_get_contents('zip://'.$zipfile.'#'.$zip_entry));
    }

    /**
     * Write the .desc file
     * @param str basename Basename of the file - without extension, but with (optional) full path
     */
    public function writeDesc($basename) {
      $desc = $this->getDesc();
      if ( !empty($desc) ) file_put_contents("${basename}.desc", $desc);
    }

    /**
     * Write the .data file
     * @param str basename Basename of the file - without extension, but with (optional) full path
     */
    public function writeData($basename) {
      $data = $this->getData();
      if ( !empty($data) ) file_put_contents("${basename}.".$this->dataExt, $data);
    }

    /**
     * Write the cover image file
     * @param str basename Basename of the file - without extension, but with (optional) full path
     * @return bool success
     */
    public function writeCover($basename) {
      $item = $this->getCover();
      if ( !empty($item) ) {
        $ext = preg_replace('!.*\.([^\.]+)$!','$1',$item['href']);
        if ( $ext == 'jpeg' ) $ext = 'jpg';
        return $this->extract_file($this->ebookDir, $item['href'], $basename .'.'. $ext);
      }
    }

    /**
     * Write the .data, .desc, and cover files
     * @param str basename Basename of the file - without extension, but with (optional) full path
     */
    public function writeFiles($basename) {
      $this->writeDesc($basename);
      $this->writeData($basename);
      $this->writeCover($basename);
    }

    /**
     * Obtain the .data content
     * @return str data
     */
    public function getData() {
      if ( $this->data === NULL ) $this->_getMeta();
      return $this->data;
    }

    /**
     * Add a line to the .data content
     * @param str line
     */
    protected function addData($line) {
      if ( !empty($line) ) {
        $split = explode('::',$line);
        if ( in_array($split[0],$this->extract2data) || in_array('all',$this->extract2data) ) $this->data .= "${line}\n";
      }
    }

    /**
     * Obtain the .desc content
     * @return str desc
     */
    public function getDesc() {
      if ( $this->desc === NULL ) $this->_getMeta();
      return $this->desc;
    }

    /**
     * Add a line to the .desc content header, taking care for line breaks
     * @param str line
     */
    protected function addDescHead($line) {
      if ( !(in_array('all',$this->extract2desc) || in_array('head',$this->extract2desc)) ) return;
      if ( empty($this->desc) ) $this->desc = $line;
      else {
        if ( $this->type == 'html' ) $this->desc .= "\n${line}";
        elseif ( $this->type == 'md' ) $this->desc .= "  \n${line}";
        else trigger_error("Unknown desc format set: '".$this->type."'", E_USER_WARNING);
      }
    }

    /**
     * Add a line to the .desc content "body" (aka "as-is")
     * @param str line
     */
    protected function addDescRaw($line) {
      $this->desc .= $line;
    }

    /**
     * Parse a "person" item and respect its "role" property
     * @param array item
     * @brief used for dc:creator & dc:contributor
     */
    protected function parsePerson($item) { // used for dc:creator & dc:contributor
      if ( is_array($item) && !empty($item) ) foreach($item as $it) {
        if ( !isset($it['role']) ) { $this->addDescHead($this->terms['author'].": ${it['value']}"); continue; }
        switch ($it['role']) {
          case 'aut': $this->addDescHead($this->terms['author'].": ${it['value']}"); $this->addData("author::${it['value']}"); break;
          case 'bkp': $this->addDescHead($this->terms['bookproducer'].": ${it['value']}"); break;
          case 'edt': $this->addDescHead($this->terms['editor'].": ${it['value']}"); break;
          case 'ill': $this->addDescHead($this->terms['illustrator'].": ${it['value']}"); break;
          case 'pbl': $this->addDescHead($this->terms['publisher'].": ${it['value']}"); break;
          case 'red': $this->addDescHead($this->terms['redactor'].": ${it['value']}"); break;
          case 'trl': $this->addDescHead($this->terms['translator'].": ${it['value']}"); break;
          default: $this->addDescHead($this->terms['collaborator'].": ${it['value']}"); break;
        }
      }
    }

    /**
     * Parse a source item (DcSource & identifier) for where the eBook was taken from (where it originated)
     * @param str item
     */
    protected function parseSource($item) { // DcSource & identifier
      if ( preg_match('!^http.+gutenberg!',$item) ) $this->addDescHead($this->terms['source'].": [Project Gutenberg](${item})");
      elseif ( preg_match('!^http.+wikisource!',$item) ) $this->addDescHead($this->terms['source'].": [WikiSource](${item})");
      elseif ( preg_match('!^http.+mobileread!',$item) ) $this->addDescHead($this->terms['source'].": [MobileRead](${item})");
      elseif ( !empty($item) ) $this->addDescHead($this->terms['source'].": [Src](${item})");
    }

    /**
     * Obtain the metadata and setup data + desc
     */
    protected function _getMeta() {
      // init strings
      $this->desc = '';
      $this->data = '';

      // Book Title
      $title = $this->getDcItem('title');
      if ( empty($title) ) $title = $epubname;
      $this->addDescHead("<u>${title}</u>");
      $this->addData("title::${title}");

      // Creator, Contributor, Publisher
      $item = $this->getDcItemFull('creator'); $this->parsePerson($item);
      $item = $this->getDcItemFull('contributor'); $this->parsePerson($item);
      $item = $this->getDcItem('publisher'); if ( !empty($item) ) {
        $this->addDescHead($this->terms['publisher'].": ${item}");
        $this->addData("publisher::${item}");
      }

      // Dates
      $item = $this->getDcItemFull('date');
      if ( is_array($item) && !empty($item) ) foreach($item as $it) {
        if ( !isset($it['event']) )  { $this->addDescHead($this->terms['date'].": ${it['value']}"); continue; }
        switch ($it['event']) {
          case 'publication'         : $this->addDescHead($this->terms['published'].": ${it['value']}"); break;
          case 'original-publication': $this->addDescHead($this->terms['firstpublished'].": ${it['value']}"); break;
          case 'ops-publication'     : $this->addDescHead($this->terms['ebookPublished'].": ${it['value']}"); break;
          case 'creation'            : $this->addDescHead($this->terms['ebookCreated'].": ${it['value']}"); break;
          case 'modification'        : $this->addDescHead($this->terms['modification'].": ${it['value']}"); break;
          default                    : $this->addDescHead($this->terms['date'].": ${it['value']}"); break;
        }
      }

      // Keywords and Copyrights
      $item = $this->getDcItem('subject');
      if (is_array($item))
        foreach ($item as $it) $this->addDescHead($this->terms['topic'].": ${it}");
      elseif (!empty($item)) $this->addDescHead($this->terms['topic'].": ${item}");
      $item = $this->getDcItem('rights'); if ( !empty($item) ) $this->addDescHead($this->terms['copyright'].": ${item}");

      // Source
      $this->parseSource( $this->getDcItem('source') );
      $this->parseSource( $this->getDcItem('relation') );

      // Identifiers
      foreach( $this->getDcItemFull('identifier') as $it ) {
        if ( isset($it['scheme']) && strtoupper($it['scheme'])=='ISBN' ) $this->addData("isbn::${it['value']}");
        elseif ( isset($it['scheme']) && strtoupper($it['scheme'])=='URI' ) $this->parseSource($it['value']);
        elseif ( preg_match('!^(\d{13}|\d{10})$!',$it['value']) ) $this->addData("isbn::${it['value']}");
        elseif ( preg_match('!^urn:isbn:(.+)$!',$it['value'],$match) ) $this->addData("isbn::".$match[1]);
        elseif ( preg_match('!^uri:(.+)$!',$it['value'],$match) ) $this->parseSource($match[1]);
      }

      // Description & TOC
      $item = $this->getDcItem('description');
      if ( !empty($item) ) {
        if ( !empty($this->desc) ) $this->addDescRaw("\n\n");
        $this->addDescRaw(nl2br($item));
      }
      if ( (in_array('all',$this->extract2desc)||in_array('toc',$this->extract2desc)) && $this->tocLevels > 0 ) {
        $item = $this->getTOC();
        if ( !empty($item) ) {
          $this->addDescRaw("\n\n<u>".$this->terms['content']."</u>\n\n");
          if ( $this->type == 'html' ) $this->addDescRaw("<ul>\n");
          foreach($item as $it) $this->_mkTocList($it,1);
          if ( $this->type == 'html' ) $this->addDescRaw("</ul>\n");
        }
      }
    }

    /**
     * convert a TOC item to a list item
     */
    protected function _mkTocList($item,$level) {
      $indent = str_repeat(' ',($level -1) * 4);
      if ( $this->type == 'md' ) $line = '* '.$item['naam'];
      else $line = '<li>'.$item['naam'].'</li>';
      $this->addDescRaw($indent.$line."\n");
      if ( $level < $this->tocLevels && !empty($item['subtoc']) ) {
          if ( $this->type == 'html' ) $this->addDescRaw($indent."<ul>\n");
          foreach ($item['subtoc'] as $it) $this->_mkTocList($it,$level +1);
          if ( $this->type == 'html' ) $this->addDescRaw($indent."</ul>\n");
      }
    }

    /**
     * Destructor
     */
    function __destruct() {
      // do we need something here?
      parent::__destruct();
    }

}