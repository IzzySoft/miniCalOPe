<?php
 #############################################################################
 # miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 # ------------------------------------------------------------------------- #
 # Translation handling methods                                              #
 #############################################################################

 /* $Id$ */

 require_once(dirname(__FILE__).'/csv.php');

 /** Translation handling methods
  * @package Api
  * @class translation
  * @author Izzy (izzysoft AT qumran DOT org)
  * @copyright (c) 2004-2009 by Itzchak Rehberg and IzzySoft
  */
 class translation {

   /** Initialize
    * @constructor translation
    * @param string langpath where do the translation files reside
    */
   function __construct($langpath,$fallback) {
     $this->langpath     = $langpath;
     $this->avail        = $this->avail();
     $this->browserpref  = $this->get_browserlang();
     if ( empty($this->browserpref) ) $this->browserpref = $fallback;
     $this->translations = array();
   }

   /** Read in all available translations for a given language
    * @class translation
    * @method get_translations
    * @param string lang_id ISO code of the prefered language
    */
   function get_translations() {
     $trans = array();
     $lang_id = $this->browserpref;
     if ( $lang_id!="en" ) {
       $filename = $this->langpath."/trans.en";
       if ( !file_exists($filename) ) return $trans;
       $trans = $this->read_translations($filename,$trans);
     }
     $filename = $this->langpath."/trans.$lang_id";
     if ( !file_exists($filename) ) return $trans;
     $this->translations = $this->read_translations($filename,$trans);
   }

   /** Read translations from CSV file
    *  (helper for get_translations)
    * @method private read_translations
    * @param string filename name of the file to read from
    * @param array trans array of translations to append to
    */
   function read_translations($filename,$trans) {
     $csv = new csv(";",'"',TRUE,FALSE);
     $csv->import($filename);
     $dc = count($csv->data);
     for ($i=0;$i<$dc;++$i) {
       $trans[$csv->data[$i]["ref"]] = $csv->data[$i]["trans"];
     }
     return $trans;
   }


   /** Get list of available language files
    * @class translation
    * @method favail
    * @return array lang_keys ( n=&gt;key )
    */
   function favail() {
     $prefix  = "trans";
     $dir = dir($this->langpath);
     while ( $file=$dir->read() ) {
       if ( strpos($file,$prefix)===0 ) {
         $pos    = strpos($file,".");
         $lang[] = substr($file,$pos+1);
       }
     }
     return $lang;
   }

   /** Get list of available languages
    * @class translation
    * @method avail
    * @return array lang_keys ( n=&gt;key )
    */
   function avail() {
     return $this->favail();
   }

   /** translate a given reference
    *  if no translation is found, the content of the passed param $key is
    *  returned instead
    * @class translation
    * @method trans
    * @param string key translation key
    * @param optional array m1 replace placeholder (%1..%10 in translations)
    * @param optional string m2 instead of $m1 being an array, all 10 replacements may be passed separately as strings
    */
   function transl($key,$m1="",$m2="",$m3="",$m4="",$m5="",$m6="",$m7="",$m8="",$m9="",$m10="") {
     if (is_array($m1)){
       $vars = $m1;
     } else {
       $vars = array($m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10);
     }
     return $this->translate("$key",$vars);
   }

   /** build the translations (helper func to method trans)
    * @class translation
    * @method translate
    * @param string key translation key
    * @param optional array vars array of replacement strings (see function lang)
    */
   public function translate($key,$vars=FALSE) {
     $trans = $this->translations[strtolower($key)];
     if (!$trans) $trans = $key;
     if (!$vars) $vars=array();
     $ndx = 1;
     while ( list($k,$v)=each($vars) ) {
       $trans = preg_replace("/%$ndx/",$v,$trans);
       $ndx++;
     }
     return $trans;
   }

   /** retrieve users prefered languages from browser info
    * @class translation
    * @method get_browserlang
    * @return string language (ISO language code) - empty if we can't support any
    */
   function get_browserlang() {
     if (!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) return ""; // prevent notice
     $langs = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
     $lc    = count($langs);
     for ($i=0;$i<$lc;++$i) { // check if we have a suitable file
       $want = substr($langs[$i],0,2);
       if (in_array($want,$this->avail)) {
         $this->browserpref = $want;
         return $want;
       }
     }
     return "";
   }

   /** Visitors prefered language according to browser info
    * @class translation
    * @attribute string browserpref
    */
   /** Translations
    * @class translation
    * @attribute array translations
    */
   /** Available languages
    * @class translation
    * @attribute array avail
    */

 } // end class trans

?>