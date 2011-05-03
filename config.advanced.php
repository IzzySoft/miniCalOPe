<?php
#############################################################################
# miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
# ------------------------------------------------------------------------- #
# Advanced configuration (use language-specific stuff)                      #
#############################################################################
# $Id$

#================================[ Directories ]===
// default database file (override in language specific settings)
$dbfile = dirname(__FILE__).'/metadata.db';
// Where your books are stored.
$bookroot    = 'books';
// Which book formats you want to use (file extensions). supported are epub and mobi
$bookformats = array('epub','mobi');
// file extension for book descriptions
$bookdesc_ext = array('desc');
// file extension for Metadata
$bookmeta_ext = 'data';

#===============================[ Checking ]===
// check for possible XML errors in description files and log them?
$check_xml = TRUE;
// as XML errors break OPDS, better skip the broken content.
// Needs above check to be enabled in order to work.
$skip_broken_xml = TRUE;

#================================[ Logging ]===
// Logfile to use. Empty means no logging to file.
$logfile = './minicalope.log';
// Seperate log file to log downloads to (in addition to the normal log).
// Empty string to disable for now.
$dllogfile = './minical_dl.log';
// Available log levels: NOLOG, EMERGENCY, ALERT, CRITICAL, ERROR, WARNING, NOTICE, INFO, DEBUG
// Yes - case IS important. Specified level includes all before-mentioned
// File log level
$fileloglevel = INFO;
// Screen output when running in web mode
$screenloglevel = NOLOG;
// Screen output when running from command line
$screenloglevel_cli = INFO;

#============================[ Book Covers ]===
// Where to get the covers: calibre, simple, or off (none)
$cover_mode = 'off';
// maximum width to display them
$cover_width = '200px';
// where to place the cover img links
$cover_base = 'covers';

#============================[ Web Service ]===
// Timezone
$timezone = '+01:00';
// Site Title
$sitetitle = 'Book Server';
// Full URL to miniCalOPe
$baseurl = 'http://localhost/opds/';
// Path relative to the web servers DOCUMENT_ROOT
$relurl  = '/opds/';
// how many items per page
$perpage = 25;
// Full URL to the Wikipedia to use for author info
$wikibase= 'http://de.wikipedia.org/wiki/';
// Enable some ISBN searches (empty array to disable this feature)
$isbnservices = array('Amazon.DE','Bookzilla.DE','Buchhandel.DE','Google.DE','Buchfreund.DE','ZVAB.COM');

#============================[ Person Info ]===
// about you: Name, Homepage, Email
$owner   = 'John Doe';
$homepage= 'http://www.johndoe.com/';
$email   = 'john@johndoe.com';

#=========================[ Language dependent stuff ]===
# Here we make use of the language-specific directories. You either can set
# the $use_lang variable directly in your scan scripts for command-line use
# (takes precedence), or pass "?lang=<lang>" in the URL.
// check what language shall be used
if ( !isset($use_lang) && isset($_REQUEST['lang']) ) $use_lang = $_REQUEST['lang'];
elseif ( !isset($use_lang) ) $use_lang = '';
// define language-specific settings
switch ($use_lang) {
    case 'cal': // not a lang - but use the Calibre DB
        $dbfile   = '/mnt/data/eBooks/metadata.db';
        break;
    case 'en':
        $uselangs = array('en');
        $dbfile   = dirname(__FILE__).'/metadata_en.db';
        $isbnservices = array('Amazon.COM','Google.COM','BookCrossing.COM','EuroBuch.COM');
        break;
    default  :
        $uselangs = array('de');
        break;
}

?>