<?php
#############################################################################
# miniCalOPe                               (c) 2010-2015 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
#############################################################################

class epub {
  /**
   * ePub Reader class
   *
   * @package     Api
   * @author      Andreas Itzchak Rehberg
   * @author      Tristan Siebers
   * @license     LGPL
   * @info based on the Readepub class written by Tristan Siebers, see https://github.com/trizz/PHP-ePub-Reader
   */

    /**
     * Contains the path to the dir with the ePub files
     * @var string Path to the extracted ePub files
     */
    var $ebookDir;
    /**
     * ZipArchive instance with contents of the $ebookDir
     * @var object zip
     */
    var $zip;
    /**
     * Whether this class instance was initialized successfully
     * @var bool initOK
     */
    var $initOK = FALSE;
    /**
     * Holds the (relative to $ebookDir) path to the OPF file
     * @var string Location + name of OPF file
     */
    var $opfFile;
    /**
     * Holds the content of the OPF file
     * @var protected $opfContents
     */
    protected $opfContents;
    /**
     * Relative (to $ebookDir) OPF (ePub files) dir
     * @var type Files dir
     */
    var $opfDir;
    /**
     * Holds all the found DC elements in the OPF file in a simple array
     * @var array All found DC elements in the OPF file
     * @see getDcItem()
     */
    var $dcElements;
    /**
     * Holds all the found DC elements in the OPF file in a complex array
     * @var array All found DC elements in the OPF file
     * @see getDcElementsFull()
     */
    var $dcItemFull;
    /**
     * Holds all the manifest items of the OPF file
     * @var array All manifest items
     */
    var $manifest;
    /**
     * Holds all the spin data
     * @var array Spine data (array of strings)
     * @see getSpine()
     */
    protected $spine = NULL;
    /**
     * Holds the ToC data
     * @var array Array with ToC items
     * @see getTOC()
     */
    protected $toc = NULL;
    /**
     * cover data
     * @var protected cover
     * @see getCover()
     */
    protected $cover = NULL;

    /**
     * Constructor
     * @param ebookDir the *.epub file to analyze
     */
    public function __construct($ebookDir) {
      if ( empty($ebookDir) ) {
        trigger_error("eBook file name cannot be empty!", E_USER_WARNING);
        return;
      }
      $this->init($ebookDir);
    }

    /**
     * Destructor
     */
    function __destruct() {
        $this->zip->close();
    }

    /**
     * Analyze an *.epub file and fill the class variables
     * @param string ebookDir *.epub file to analyze
     * @return bool success of initialization
     */
    public function init($ebookDir) {
        if ( !file_exists($ebookDir) ) {
          trigger_error("eBook '$ebookDir' not found", E_USER_WARNING);
          $this->initOK = FALSE;
          return;
        }

        $this->ebookDir = $ebookDir;
        $this->zip = new ZipArchive();
        $this->zip->open($this->ebookDir); // gets closed in this::__destruct()

        $this->_getOPF();
        $this->opfContents = simplexml_load_string($this->getZipFile($this->opfFile));

        $this->_getDcData();
        $this->_getManifest();

        $this->initOK = TRUE;

        //$this->debug();
    }

    /**
     * Get content of a specific ZIP file
     * @param string zipfile (path and) name of the file to "extract"
     * @return string file_content
     * @brief relies on this::zip being initialized and opened
     */
    public function getZipFile($zipfile) {
        $buf = '';
        if ( !$fp = $this->zip->getStream($zipfile) ) {
          trigger_error("Could not extract '$zipfile' from '".$this->zip->filename."'", E_USER_WARNING);
          return $buf;
        }
        ob_start(); //to capture CRC error message
        while (!feof($fp)) $buf .= fread($fp, 2048);
        $s = ob_get_contents();
        ob_end_clean();
        if ( stripos($s, "CRC error") != FALSE ) trigger_error("CRC32 mismatch, current: '".printf("%08X", crc32($buf))."', expected: '".printf("%08X", $stat['crc'])."'");
        fclose($fp);
        return $buf;
    }

    /**
     * Get list of DC items the book has defined
     * @return array DcItems array[0..n] of string
     */
    public function getDcItemNames() {
      return array_keys($this->dcElements);
    }

    /**
     * Get the specified DC item
     * @param string $item The DC Item key
     * @return mixed String/Array when DC item exists, otherwise false
     */
    public function getDcItem($item) {
        if(array_key_exists($item, $this->dcElements)) {
            return $this->dcElements[$item];
        } else {
            return false;
        }
    }

    /**
     * Get the specified DC item with its attributes
     * @param string $item The DC Item key
     * @return mixed Array when DC item exists, otherwise false
     * @short array[0..n] always has a field 'value', and may have additional fields for attributes.
     */
    public function getDcItemFull($item) {
        if(array_key_exists($item, $this->dcElementsFull)) {
            return $this->dcElementsFull[$item];
        } else {
            return false;
        }
    }

    /**
     * Get the specified manifest item
     * @param string $item The manifest ID
     * @return string/boolean String when manifest item exists, otherwise false
     */
    public function getManifest($item) {
        if(array_key_exists($item, $this->manifest)) {
            return $this->manifest[$item];
        } else {
            return false;
        }
    }

    /**
     * Get the specified manifest by type
     * @param string $type The manifest type
     * @return mixed Array when manifest items exists, otherwise false
     */
    public function getManifestByType($type) {
        foreach($this->manifest AS $manifestID => $manifest) {
            if($manifest['media-type'] == $type) {
                $return[$manifestID]['href'] = $manifest['href'];
                $return[$manifestID]['media-type'] = $manifest['media-type'];
            }
        }

        return (count($return) == 0) ? false : $return;
    }

    /**
     * Get the specified manifest by type
     * @param string $type The manifest type RegEx
     * @return mixed items Array when manifest item exists, otherwise false
     */
    public function getManifestByTypeMatch($type) {
        $return = array();

        foreach($this->manifest AS $manifestID => $manifest) {
            if( preg_match($type,$manifest['media-type']) ) {
                $return[$manifestID]['href'] = $manifest['href'];
                $return[$manifestID]['media-type'] = $manifest['media-type'];
            }
        }

        return (count($return) == 0) ? false : $return;
    }

    /** Get the cover
     * @return array cover (str path, str media-type, bool trueCover), empty if not found
     * @brief trueCover indicates if it was declared as cover (some epubs contain a cover
     *        image, but not declare it such; this method checks for some specific jpg/png/gif then)
     */
    public function getCover() {
      if ( $this->cover === NULL ) {
        $cover = $this->getManifest('cover-image'); // some epubs use this, and point 'cover' to the page
        if ( $cover !== FALSE ) {
          $this->cover = $cover;
          $this->cover['trueCover'] = TRUE;
          return $this->cover;
        }
        $cover = $this->getManifest('cover'); // this usually is the cover image, unless 'cover-image' was used (see above)
        if ( $cover !== FALSE ) {
          $this->cover = $cover;
          $this->cover['trueCover'] = TRUE;
          return $this->cover;
        } else { // some epubs have the cover image undeclared
          $imgs = $this->getManifestByTypeMatch('!^image/!');
          $names = array('_cover_','cover','cover-image','cover1','cover-page','titel');
          if (!empty($imgs)) foreach($names as $name) {
            foreach($imgs as $img) {
              if ( preg_match('!'.$name.'\.(jpg|jpeg|png|gif)$!',$img['href']) ) {
                $this->cover = $img;
                $this->cover['trueCover'] = FALSE;
                return $this->cover;
              }
            }
          }
        }
        $this->cover = array(); // if we reach this point, there is no cover
      }
      return $this->cover;
    }

    /**
     * Retrieve the spine
     * @return array spine (array of strings defining the reading order)
     */
    public function getSpine() {
      if ( $this->spine === NULL ) $this->_getSpine();
      return $this->spine;
    }

    /**
     * Retrieve the ToC
     * @return array Array with ToC Data
     */
    public function getTOC() {
        if ( $this->toc === NULL ) $this->_getTOC();
        return $this->toc;
    }

    /**
     * Returns the OPF/Data dir
     * @return string The OPF/data dir
     */
    public function getOPFDir() {
        return $this->opfDir;
    }

    /**
     * Prints all contents of the class directly to the screen
     */
    public function debug() {
        echo sprintf('<pre>%s</pre>', print_r($this, true));
    }

    // Private functions

    /**
     * Get the path to the OPF file from the META-INF/container.xml file
     * @return string Relative path to the OPF file
     */
    private function _getOPF() {
        $opfContents = simplexml_load_string($this->getZipFile('META-INF/container.xml'));
        $opfAttributes = $opfContents->rootfiles->rootfile->attributes();
        if (isset($opfAttributes['full-path'])) $this->opfFile = (string) $opfAttributes['full-path'];
        else $this->opfFile = (string) $opfAttributes[0]; // Typecasting to string to get rid of the XML object

        // Set also the dir to the OPF (and ePub files)
        $opfDirParts = explode('/',$this->opfFile);
        unset($opfDirParts[(count($opfDirParts)-1)]); // remove the last part (it's the .opf file itself)
        $this->opfDir = implode('/',$opfDirParts);

        return $this->opfFile;
    }

    /**
     * Read the metadata DC details (title, author, etc.) from the OPF file
     */
    private function _getDcData() {
        $this->dcElements = (array) $this->opfContents->metadata->children('dc', true);
        $opfAttribs = [];
        foreach ( $this->opfContents->metadata->children('dc',true) as $dc ) {
          $att = (array) $dc->attributes('opf',true);
          $name = $dc->getName();
          $data = ['value' => (string) $dc];
          if ( !empty($att) ) foreach ($att['@attributes'] as $var=>$val) $data[$var] = $val;
          $opfAttribs[$name][] = $data;
        }
        $this->dcElementsFull = $opfAttribs;
    }


    /**
     * Gets the manifest data from the OPF file
     */
    private function _getManifest() {
        $iManifest = 0;
        $basedir = dirname($this->opfFile);
        if ($basedir == '.') $basedir = '';
        if (!empty($basedir)) $basedir .= '/';
        foreach ($this->opfContents->manifest->item AS $item) {
            $attr = $item->attributes();
            $id = (string) $attr->id;
            $this->manifest[$id]['href'] = $basedir . (string) $attr->href;
            $this->manifest[$id]['media-type'] = (string) $attr->{'media-type'};
            $iManifest++;
        }
    }

    /**
     * Get the spine data from the OPF file
     */
    private function _getSpine() {
        foreach ($this->opfContents->spine->itemref AS $item) {
            $attr = $item->attributes();
            $this->spine[] = (string) $attr->idref;
        }
    }

    /**
     * get a single toc item and recurse into sub-items
     * @param object navpoint
     * @brief used by this::_getTOC()
     *
     */
    protected function _getTocItem($navPoint) {
        $navPointData = $navPoint->attributes();
        $po = (int)$navPointData['playOrder'];
        $toc = [];
        if ( array_key_exists($po,$toc) ) $po = max(array_values(array_keys($toc))) +1; // work-around broken values; some EPUBs have playOrder always 0
        $toc[$po]['id'] = (string)$navPointData['id'];
        $toc[$po]['naam'] = (string)$navPoint->navLabel->text;
        $toc[$po]['src'] = (string)$navPoint->content->attributes();
        if ( property_exists($navPoint,'navPoint') ) {
          foreach ($navPoint->navPoint as $subNav) {
            $subNavData = $subNav->attributes();
            $toc[$po]['subtoc'][(int)$subNavData['playOrder']] = $this->_getTocItem($subNav)[(int)$subNavData['playOrder']];
          }
        } else {
          $toc[$po]['subtoc'] = FALSE;
        }
        return $toc;
    }

    /**
     * Build an array with the TOC
     */
    protected function _getTOC() {
        $tocFile = $this->getManifest('ncx');
        if ( empty($tocFile) ) $tocFile = $this->getManifest('toc_ncx'); // malformatted epub?
        if ( empty($tocFile) ) {
          $toc = array();
          trigger_error("Could not find TOC in '".$this->ebookDir."'", E_USER_WARNING);
          return;
        }
        $tocContents = simplexml_load_string($this->getZipFile($tocFile['href']));

        $toc = array();
        foreach($tocContents->navMap->navPoint AS $navPoint) {
            $navPointData = $navPoint->attributes();
            $po = (int)$navPointData['playOrder'];
            if ( array_key_exists($po,$toc) ) $po = max(array_values(array_keys($toc))) +1; // work-around broken values; some EPUBs have playOrder always 0
            $toc[$po] = $this->_getTocItem($navPoint)[(string)$navPointData['playOrder']];
        }

        $this->toc = $toc;
    }
}