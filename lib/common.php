<?
 #############################################################################
 # miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

/** Output debug message only if debugging is enabled
 * function debug
 * @param string msg debug message to print
 */
function debugOut($msg) {
  if ($GLOBALS['debug']) echo "$msg\n";
}
?>