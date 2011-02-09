<?
#############################################################################
# miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
#############################################################################
# $Id$

/** Output debug message only if debugging is enabled
 * function debug
 * @param string msg debug message to print
 */
function debugOut($msg) {
  if ($GLOBALS['debug']) echo "$msg\n";
}

#=====================================================[ Checking URL input ]===
/** Verifying integers
 * @function req_int
 * @param string in varname Name of the _REQUEST variable
 * @param optional integer default Default value to return in case of mismatch (0)
 * @return integer value
 */
function req_int($name,$default=0) {
  if ( !isset($_REQUEST[$name]) ) return $default;
  return (int) $_REQUEST[$name];
}
/** Verifying words
 * @function req_word
 * @param string in varname Name of the _REQUEST variable
 * @param optional string default Default value to return in case of mismatch ('')
 * @return string value
 */
function req_word($name,$default='') {
  if ( !isset($_REQUEST[$name]) ) return $default;
  if (!preg_match('!^\w*$!',$_REQUEST[$name],$match)) return $default;
  return $_REQUEST[$name];
}
/** Verifying alpha-numerical input, e.g. names
 * @function rel_alnum
 * @param string in varname Name of the _REQUEST variable
 * @param optional string default Default value to return in case of mismatch ('')
 */
function req_alnum($name,$default='') {
  if ( !isset($_REQUEST[$name]) ) return $default;
  if ( preg_match('![^\w\s_-\pL]!u',$_REQUEST[$name],$match) ) return $default;
  return $_REQUEST[$name];
}
/** Verifying array of integer (HTML multi-select form element)
 * @function req_intarr
 * @param string in varname Name of the _REQUEST variable
 * @param optional mixed default Default value to return in case of mismatch (array())
 */
function req_intarr($name,$default=array()) {
  if ( !isset($_REQUEST[$name]) ) return $default;
  if ( !is_array($_REQUEST[$name]) ) return $default;
  foreach ($_REQUEST[$name] as $val) {
    if ( !is_numeric($val) ) return $default;
  }
  return $_REQUEST[$name];
}
?>