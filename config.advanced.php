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
// how to insert books into the database:
// 'rebuild': drop all data and do a fresh insert (books are likely to get new IDs this way)
// 'merge'  : try to figure out what has changed (experimental; keeps the book IDs, but is slower)
$scan_dbmode = 'rebuild';

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
// Report New/Moved/Deleted on Scan
$scan_report_changes = FALSE;

#============================[ Book Covers ]===
// Where to get the covers: calibre, simple, or off (none)
$cover_mode = 'off';
// maximum width (in px) to display them
$cover_width = '200';
// where to place the cover img links
$cover_base = 'covers';
// generate fake-covers when no real img available (HTML only)?
$cover_fake_fallback = TRUE;

#==========================[ Scan Specials ]===
// when found in .data files, what shall override definitions collected from the
// directory structure? If not named here, it will be "merged". Currently
// supported: author
$data_overrides = array('author');
// As it's advisable to use 7-bit ASCII only for directory and file names,
// one might wish to get the "correct spelling" into the Metadata by other means.
// You can place ".name" files to have the directory name "replaced". Keep in
// mind, however, that non-ASCII characters might cause issues with sorting.
// Also remember to keep the .name and the "tag::"/"author::" in sync with .data files :)
$dotname_overrides = array('author','genre');

#============================[ Web Service ]===
// Timezone
$timezone = '+01:00';
// Site Title
$sitetitle = 'Book Server';
// Full URL to miniCalOPe
$baseurl = 'http://'.$_SERVER['SERVER_NAME'].'/opds/';
// Path relative to the web servers DOCUMENT_ROOT
$relurl  = '/opds/';
// how many items per page
$perpage = 25;
// Full URL to the Wikipedia to use for author info
$wikibase= 'http://de.wikipedia.org/wiki/';
// Enable some ISBN searches (empty array to disable this feature)
$isbnservices = array('Amazon.DE','Bookzilla.DE','Buchhandel.DE','Google.DE','Buchfreund.DE','ZVAB.COM');
// Enable some book search services (search by author and title of the selected book;
// empty array to disable this feature)
$booksearchservices = array('Amazon.DE','Bookzilla.DE','Google.DE','Buchfreund.DE','ZVAB.COM');

#============================[ Person Info ]===
// about you: Name, Homepage, Email, Amazon PartnerID (leave empty if you have none)
$owner   = 'John Doe';
$homepage= 'http://www.johndoe.com/';
$email   = 'john@johndoe.com';

#=========================[ Monetarization ]===
// While our content is served for free, we won't object to any donations :)
// -=[ Flattr ]=-
// Setting your FlattrID here will enable the FlattR button
$flattrID = '';
// -=[ Amazon ]=-
// Your AmazonID will be used for Amazon ads (see below) as well as for the ISBN
// and book-search services (see above)
$amazonID='';
// Amazon ad content for book details page? (needs AmazonID, see Personal Info)
$ads_bookdetails = TRUE;
$ads_bordercolor = '4D9DFC';
$ads_logocolor   = 'AA4400';

#=========================[ Language dependent stuff ]===
# Here we make use of the language-specific directories. You either can set
# the $use_lang variable directly in your scan scripts for command-line use
# (takes precedence), or pass "?lang=<lang>" in the URL.
// check what language shall be used
if ( !isset($use_lang) && isset($_REQUEST['lang']) && !empty($_REQUEST['lang']) ) $use_lang = $_REQUEST['lang'];
elseif ( empty($use_lang) ) $use_lang = 'de'; // define fallback to default lang
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