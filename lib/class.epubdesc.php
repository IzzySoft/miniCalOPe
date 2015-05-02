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
    protected $desc;

    /**
     * Holds the .data content. Obtain with getData().
     * @protected str data
     */
    protected $data;

    /**
     * Our "dictionary" for text output. For setting up yours, see setDict()
     * @protected array terms
     */
    protected $terms = array(
      'author' => 'Autor',
      'bookproducer' => 'eBook Ersteller',
      'collaborator' => 'Mitarbeiter',
      'copyright' => 'Copyright',
      'date' => 'Datum',
      'editor' => 'Bearbeitung',
      'ebookPublished' => 'eBook veröffentlicht',
      'ebookCreated' => 'eBook erstellt',
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
      $this->desc = '';
      $this->data = '';
      $this->_getMeta();
    }

    /**
     * Extract a sincle file from the eBook
     * @param str zipfile filename to extract from (the *.epub)
     * @param str zip_entry name of the file to extract (with path from tze zip root, if any)
     * @param str target full path and file name of where the extracted file should be placed
     */
    function extract_file($zipfile,$zip_entry,$target) {
      if ( substr($zipfile,0,1) != '/' ) $zipfile = './'.$zipfile;
      file_put_contents($target,file_get_contents('zip://'.$zipfile.'#'.$zip_entry));
    }

    /**
     * Write the .data, .desc, and cover files
     * @param str basename Basename of the file - without extension, but with (optional) full path
     */
    public function writeFiles($basename) {
      file_put_contents("${basename}.desc", $this->getDesc());
      file_put_contents("${basename}.data", $this->getData());
      $item = $this->getCover();
      if ( !empty($item) ) {
        $ext = preg_replace('!.*\.([^\.]+)$!','$1',$item['href']);
        if ( $ext == 'jpeg' ) $ext = 'jpg';
        $this->extract_file($this->ebookDir, $item['href'], $basename .'.'. $ext);
      }
    }

    /**
     * Obtain the .data content
     * @return str data
     */
    public function getData() {
      return $this->data;
    }

    /**
     * Add a line to the .data content
     * @param str line
     */
    protected function addData($line) {
      if ( !empty($line) ) $this->data .= "${line}\n";
    }

    /**
     * Obtain the .desc content
     * @return str desc
     */
    public function getDesc() {
      return $this->desc;
    }

    /**
     * Add a line to the .desc content header, taking care for line breaks
     * @param str line
     */
    protected function addDescHead($line) {
//echo "Adding line to desc: '$line'\n";
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
//echo "parsePerson\n";
//print_r($item);
      if ( is_array($item) && !empty($item) ) foreach($item as $it) {
        if ( !isset($it['role']) ) { $this->addDescHead("Autor: ${it['value']}"); continue; }
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
        if ( !isset($it['event']) )    $this->addDescHead($this->terms['date'].": ${it['value']}"); continue;
        switch ($it['event']) {
          case 'original-publication': $this->addDescHead($this->terms['published'].": ${it['value']}"); break;
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
      if ( !empty($item) ) $this->addDescRaw("\n\n${item}");
      $item = $this->getTOC();
      if ( !empty($item) ) {
        $this->addDescRaw("\n\n");
        foreach($item as $it) $this->addDescRaw("* ".$it['naam']."\n");
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