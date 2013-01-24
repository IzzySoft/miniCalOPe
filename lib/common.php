<?php
#############################################################################
# miniCalOPe                               (c) 2010-2011 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
#############################################################################
# $Id$

require_once(dirname(__FILE__).'/logging.php'); // just in case

// Central Logger instance
if (IS_CLI) {
  $logger   = new logging($logfile,  $fileloglevel,$screenloglevel_cli);
  $dllogger = new logging($dllogfile,$fileloglevel,$screenloglevel_cli);
} else {
  $logger   = new logging($logfile,  $fileloglevel,$screenloglevel);
  $dllogger = new logging($dllogfile,$fileloglevel,$screenloglevel);
}

#=====================================================[ Checking URL input ]===
/** Verifying integers
 * @function req_int
 * @param string in varname Name of the _REQUEST variable
 * @param optional integer default Default value to return in case of mismatch (0)
 * @return integer value
 */
function req_int($name,$default=0) {
  if ( !isset($_REQUEST[$name]) ) {
    $GLOBALS['logger']->debug("URLParam '$name' was expected but is not set",'WEBIF');
    return $default;
  }
  $val = (int) $_REQUEST[$name];
  if ( $val!=$_REQUEST[$name] ) {
    $GLOBALS['logger']->warn("URLParam '$name' expected to be INT - got '".$_REQUEST[$name]."'",'WEBIF');
  }
  return $val;
}
/** Verifying words
 * @function req_word
 * @param string in varname Name of the _REQUEST variable
 * @param optional string default Default value to return in case of mismatch ('')
 * @return string value
 */
function req_word($name,$default='') {
  if ( !isset($_REQUEST[$name]) ) {
    $GLOBALS['logger']->debug("URLParam '$name' was expected but is not set",'WEBIF');
    return $default;
  }
  if (!preg_match('!^\w*$!',$_REQUEST[$name],$match)) {
    $GLOBALS['logger']->warn("URLParam '$name' expected to be WORD - got '".$_REQUEST[$name]."'",'WEBIF');
    return $default;
  }
  return $_REQUEST[$name];
}
/** Verifying alpha-numerical input, e.g. names
 * @function rel_alnum
 * @param string in varname Name of the _REQUEST variable
 * @param optional string default Default value to return in case of mismatch ('')
 */
function req_alnum($name,$default='') {
  if ( !isset($_REQUEST[$name]) ) {
    $GLOBALS['logger']->debug("URLParam '$name' was expected but is not set",'WEBIF');
    return $default;
  }
  if ( preg_match('![^\w\s_-\pL]!u',$_REQUEST[$name],$match) ) {
    $GLOBALS['logger']->warn("URLParam '$name' expected to be ALNUM - got '".$_REQUEST[$name]."'",'WEBIF');
    return $default;
  }
  return $_REQUEST[$name];
}
/** Verifying alpha-numerical input with wildcards, e.g. for search mask
 * @function rel_alnumwild
 * @param string in varname Name of the _REQUEST variable
 * @param optional string default Default value to return in case of mismatch ('')
 */
function req_alnumwild($name,$default='') {
  if ( !isset($_REQUEST[$name]) ) {
    $GLOBALS['logger']->debug("URLParam '$name' was expected but is not set",'WEBIF');
    return $default;
  }
  if ( preg_match('![^\w\s\*\%\?_-\pL]!u',$_REQUEST[$name],$match) ) {
    $GLOBALS['logger']->warn("Rejecting input '".$_REQUEST[$name]."' for '$name'",'VERIFY');
    return $default;
  }
  return $_REQUEST[$name];
}
/** Verifying array of integer (HTML multi-select form element)
 * @function req_intarr
 * @param string in varname Name of the _REQUEST variable
 * @param optional mixed default Default value to return in case of mismatch (array())
 */
function req_intarr($name,$default=array()) {
  if ( !isset($_REQUEST[$name]) ) {
    $GLOBALS['logger']->debug("URLParam '$name' was expected but is not set",'WEBIF');
    return $default;
  }
  if ( !is_array($_REQUEST[$name]) ) {
    $GLOBALS['logger']->warn("URLParam '$name' expected to be ARRAY - got '".$_REQUEST[$name]."'",'WEBIF');
    return $default;
  }
  $vals = array();
  foreach ($_REQUEST[$name] as $val) {
    if ( is_numeric($val) ) $vals[] = $val;
    else {
      $globals['logger']->warn("Skipping non-numeric value '$val' in expected INTARR '$name'",'WEBIF');
    }
  }
  return $vals;
}
?>