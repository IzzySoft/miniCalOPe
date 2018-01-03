<?php
 #############################################################################
 # phpApi                                   (c) 2004-2009 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 # ------------------------------------------------------------------------- #
 # CSV File handling (import)                                                #
 #############################################################################

 /* $Id$ */

 /** CSV file handling
  * @package Api
  * @class csv
  * @author Izzy (izzysoft AT qumran DOT org)
  * @copyright (c) 2004-2009 by Itzchak Rehberg and IzzySoft
  */
 class csv {

   /** CVS data: array[0..n] of imported records.
    *  Each Record is an array[0..n] of objects with the properties: name,data
    * @class csv
    * @attribute array data
    */
   /** CVS field separator (defaults to ";")
    * @class csv
    * @attribute string sep
    */
   /** CVS text field encloser (defaults to '"')
    * @class csv
    * @attribute string txt
    */
   /** trim imported values? (Defaults to FALSE)
    * @class csv
    * @attribute boolean trim
    */

   /** Setting up defaults
    * @constructor csv
    * @param optional string separator (default: ';')
    * @param optional string textmarker
    *   (by what characters are textfields enclosed; default: '"')
    * @param optional boolean trim whether to trim the fields
    * @param optional mixed recode either recode definition string
    *  (e.g. "lat1..utf-8", see method recode) or FALSE for no recoding)
    */
   function __construct($sep=";",$txt='"',$trim=FALSE,$recode="lat1..utf-8") {
     $this->sep  = $sep;
     $this->txt  = $txt;
     $this->trim = $trim;
     $this->recode = $recode;
     $this->data = array();
   }

   /** Import CSV file
    * @class csv
    * @method import
    * @param string filename file to import
    */
   function import($filename) {
     $this->read_file($filename);
     $pos = strpos($this->text,"\n");
     $line = trim(substr($this->text,0,$pos));
     $this->text = substr($this->text,$pos+1);
     $this->read_fields($line);
     $this->read_data();
   }

   /** Get the field names
    *  Reads the field names from the specification line and sets up the
    *  this::field array
    * @class csv
    * @method read_fields
    * @param string line field specification line from the CSV file to import
    */
   function read_fields($line) {
     $fields = explode($this->sep,$line);
     $fc = count($fields);
     for ($i=0;$i<$fc;++$i) {
       $this->field[$i] = new stdClass();
       if (substr($fields[$i],0,1)==$this->txt) {
         $name = substr($fields[$i],1,strlen($fields[$i])-2);
         $this->field[$i]->txt  = TRUE;
       } else {
         $name = $fields[$i];
         $this->field[$i]->txt  = FALSE;
       }
       $this->field[$i]->name = $name;
       $this->fieldid[$name] = $i;
       if ($this->trim) $this->field[$i]->name = trim($this->field[$i]->name);
       $this->field[$i]->name = $this->recode($this->field[$i]->name);
     }
     if ( $this->field[$fc -1]->txt ) {
       $this->rec_end = $this->txt."\n";
     } else {
       $this->rec_end = "\n";
     }
   }

   /** Get the file content
    *  Reads the CSV file into the internal string $this->text
    * @class csv
    * @method read_file
    * @param string filename file to read
    */
   function read_file($filename) {
     if ( !file_exists($filename) || !is_readable($filename) ) trigger_error("Failed to open '$filename' for read",E_USER_NOTICE);
     $buffer = file_get_contents($filename);
     $this->text = preg_replace("/\r?\n|\r/", "\n", $buffer);
   }

   /** Get CSV data
    *  Reads data from the imported CSV file and stores it into
    *  the $this->data array (array of ojects with the elements: name, data)
    * @class csv
    * @method read_data
    */
   function read_data() {
     $fc = count($this->field);
     $rec = 0;
     while (!empty($this->text)) {
       $pos  = strpos($this->text,$this->rec_end);
       $line = trim(substr($this->text,0,$pos));
       if (empty($line)) {
         $this->text = substr($this->text,$pos+1);
       } else {
         for ($i=0;$i<$fc;++$i) { // record start
           $name = $this->field[$i]->name;
           if ($this->field[$i]->txt) {
             if ($i+1<$fc) {
               $pos = strpos($this->text,$this->txt.$this->sep,1);
             } else {
	       $pos = strpos($this->text,$this->txt."\n");
             }
             $data[$name] = substr($this->text,1,$pos-1);
             $this->text = substr($this->text,$pos+2);
           } else {
             if ($i+1<$fc) {
               $pos = strpos($this->text,$this->sep);
             } else {
               $pos = strpos($this->text,"\n");
             }
             $data[$name] = substr($this->text,0,$pos);
             $this->text = substr($this->text,$pos+1);
           }
           if ($this->trim) $data[$name] = trim($data[$name]);
	   $data[$name] = $this->recode($data[$name]);
           $this->data[$rec] = $data;
         } // record end
       }
       ++$rec;
     }
   }

   /** Clear all data
    *  Remove all internal data, e.g. to import a different file
    * @class csv
    * @method clear_data
    */
   function clear_data() {
     unset ($this->data);
     unset ($this->field);
   }

   /** Recode a string between two character sets
    * @class csv
    * @method recode
    * @param string string to recode
    * @return string recoded string
    * @version supports only "lat1..utf-8" and vice versa for now; the recode
    *  definition has to be set in the this::recode property. Usage of an own
    *  recoding method was due to recode() and iconv() functions of PHP need
    *  special requirements (such as libraries and modules) which are not
    *  available on most systems by default.
    */
   function recode ($string) {
     switch ($this->recode) {
       case "lat1..utf-8" :
         return preg_replace("/([\x80-\xFF])/e",
             "chr(0xC0|ord('\\1')>>6).chr(0x80|ord('\\1')&0x3F)",
	     $string); break;
       case "utf-8..lat1" :
         return preg_replace("([\xC2\xC3])([\x80-\xBF])/e",
	     "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)",
	     $string); break;
       default: return($string);
     }
   }

# =========================================================[ data methods ]===
   /** Sort data by field
    * @class csv
    * @method sort
    * @param string column sort by which column?
    * @param optional string order ASC or DESC
    * @param optional string type "num" or "str" (default: AutoDetect)
    */
   function sort($column,$order="",$type="") {
     $order = strtoupper($order);
     if (empty($order)) $order = "ASC";
     $array=$this->data;
     if (sizeof($array)==0) return;
     $id = $this->fieldid[$column];
     if (empty($type)||($type!="num" && $type!="str")) {
       if ($this->field[$id]->txt) { $type="str"; } else { $type="num"; }
     }
     if ($order=="ASC") $option=""; else $option="!";
     if ($type=="str") {
       $this->array_key_multi_srt($array,$column,$option,"strcmp");
     } else {
       $this->array_key_multi_srt($array,$column,$option,"bccomp");
     }
     $this->data=$array;
   }

   function array_key_multi_srt(&$arr,$l,$option,$f='strcmp') {
     if ($f=='bccomp') $func = "return $f(\$a['$l'], \$b['$l'], 2);";
     else $func = "return $f(\$a['$l'], \$b['$l']);";
     if ($option=="!") {
       return usort($arr, create_function('$b, $a', $func));
     } else {
       return usort($arr, create_function('$a, $b', $func));
     }
   }

   /** Restrict data to a subset
    * @method where
    * @param string column name of the column to restrict by
    * @param string compare how to compare. Valid strings are: "lt", "gt", "eq", "ne"
    * @param string value to restrict by
    * @param optional string type datatype (either "str" (default) or "num")
    * @version "le" and "ge" are not yet implemented.
    */
   function where ($column,$compare,$value,$type="str") {
     if ( !in_array($compare,array('lt','gt','ne','eq','le','ge','~')) ) return;
     if ($type=="num") $f = 'bccomp';
     $dc = count($this->data);
     for ($i=0;$i<$dc;++$i) {
       switch($compare) {
         case "eq"  : if ($this->data[$i][$column]==$value) $data[] = $this->data[$i]; break;
         case "lt"  : if ($type=="num") { $val = $this->data[$i][$column]; if (bccomp($value,floatval($val),2)>0) $data[] = $this->data[$i]; }
	              else { if (strcmp(strtolower($value),strtolower($this->data[$i][$column]))>0) $data[] = $this->data[$i]; }
		      break;
	 case "gt"  : if ($type=="num") { $val = $this->data[$i][$column]; if (bccomp($value,floatval($val),2)<0) $data[] = $this->data[$i]; }
	              else { if (strcmp(strtolower($value),strtolower($this->data[$i][$column]))<0) $data[] = $this->data[$i]; }
		      break;
	 case "le"  : if ($type=="num") { $val = $this->data[$i][$column]; if (bccomp($value,floatval($val),2)>=0) $data[] = $this->data[$i]; }
	              else { if (strcmp(strtolower($value),strtolower($this->data[$i][$column]))>=0) $data[] = $this->data[$i]; }
		      break;
	 case "ge"  : if ($type=="num") { $val = $this->data[$i][$column]; if (bccomp($value,floatval($val),2)<=0) $data[] = $this->data[$i]; }
	              else { if (strcmp(strtolower($value),strtolower($this->data[$i][$column]))<=0) $data[] = $this->data[$i]; }
		      break;
	 case "ne"  : if ($this->data[$column]!=$value) $data[] = $this->data[$i]; break;
         case "~"   : if ( preg_match('|'.str_replace("%",".*",$value).'|i',$this->data[$i][$column]) ) $data[] = $this->data[$i]; break;
       }
     }
     unset($this->data);
     if (isset($data)) $this->data = $data;
     else $this->data = array();
   }

 } // end class csv
?>