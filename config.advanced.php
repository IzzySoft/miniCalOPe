<?php
#############################################################################
# miniCalOPe                               (c) 2010-2019 by Itzchak Rehberg #
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
// 'merge'  : try to figure out what has changed (recommended; keeps the book IDs, but is slower)
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
// Protect scan scripts against being run by web visitors
$scan_cli_only = TRUE;
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
// Whether to interprete content of .desc files as Markdown (1) or not (0).
// This is the global switch. If enabled (1), you can override it per-genre
// and/or per author by placing a file named .nomarkdown in their directory.
$use_markdown = 0;
#-------------------------------[ Checking ]---
// check for possible XML errors in description files and log them?
$check_xml = TRUE;
// as XML errors break OPDS, better skip the broken content.
// Needs above check to be enabled in order to work.
$skip_broken_xml = TRUE;
// how to insert books into the database:
// 'rebuild': drop all data and do a fresh insert (books are likely to get new IDs this way)
// 'merge'  : try to figure out what has changed (experimental; keeps the book IDs, but is slower)
$scan_dbmode = 'rebuild';
#------------------------[ data extraction ]---
// we can extract some details from EPUB files. Define here whether we shall do
// so, and what to extract.
// Main switch to toggle it on/off:
$autoExtract = TRUE;
// Shall we extract covers? 0=no, 1=extract, 2=extract&resize
// Covers will only be extracted if there is no cover image already.
$extractCover = 1;
// Which details to extract to .data files if autoExtract is enabled.
// Valid values are either 'all' (to extract everything), or one or a
// combination of author,isbn,publisher,rating,series,series_index,tag,title,uri
// note that while 'isbn' and 'uri' are safe to use, there might be
// issues with the others. For details, see issue #4 at Github:
// https://github.com/IzzySoft/miniCalOPe/issues/4
// Empty array switches off .data extraction completely (default)
$extract2data = array();
// What to extract to .desc files if autoExtract is enabled.
// Valid values are either 'all' (to extract everything), or one or a
// combination of 'desc' (book description only), 'head' (the heading
// part with Metadata), 'toc' (table of contents). Future versions might diverse
// further). For some background, make sure to read issue #4 at Github:
// https://github.com/IzzySoft/miniCalOPe/issues/4
// Empty array switches off .data extraction completely (default)
$extract2desc = array();

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
$wikibase= 'https://de.wikipedia.org/wiki/';
// Enable some ISBN searches (empty array to disable this feature)
$isbnservices = array('Amazon.DE','Bookzilla.DE','Buchhandel.DE','Google.DE','Buchfreund.DE','ZVAB.COM');
// Enable some book search services (search by author and title of the selected book;
// empty array to disable this feature)
$booksearchservices = array('Amazon.DE','Bookzilla.DE','Google.DE','Buchfreund.DE','ZVAB.COM');

#============================[ Person Info ]===
// about you: Name, Homepage, Email
$owner   = 'John Doe';
$homepage= 'http://www.johndoe.com/';
$email   = 'john@johndoe.com';

#=========================[ Monetarization ]===
// While our content is served for free, we won't object to any donations :)
// -=[ Donations ]-
// Specifying your Donation page here will enable the corresponding button.
// currently supported: liberapay
$donationType = '';
$donationURL = '';
// -=[ Flattr ]=-
// Setting your FlattrID here will enable the FlattR button
$flattrID = '';
// The "dynamic button" shows FlattRs already received, but exposes your visitors to 3rd party sites
$flattrMode = 'static'; // static|dynamic
// -=[ Amazon ]=-
// Your AmazonID will be used for Amazon ads (see below) as well as for the ISBN
// and book-search services (see above). Simple leave it empty if you have none.
$amazonID='';
// Amazon ad content for book details page? (needs AmazonID, see Personal Info)
$ads_bookdetails = TRUE;
$ads_bookdetails_type = 'flash'; // flash|asap
$ads_bordercolor = '4D9DFC';
$ads_logocolor   = 'AA4400';
// --[ Amazon Simple Api (ASAP) ]--
// your public and private API keys are needed for this. Leave empty if you have none,
// this automatically disables ASAP. Same applies if you have no amazonID (see above).
$ads_asap_pubkey = '';
$ads_asap_privkey = '';
$ads_asap_country = 'de';
// use additional webvertizer class
$ads_asap_webvertizer = FALSE;
$ads_asap_webvertizer_domain = 'ebooks';
// display ads on "initial pages" (i.e. those with no books, genres, authors)
$ads_asap_initial = FALSE;
// default string for "initial pages"
$ads_asap_default_string = 'keywords::Buch;prodgroup::Books,DVD,Electronics';
// file with genre specific strings (JSON; see 'lib/asap_genres.json.sample' for
// an example file). Empty string disables this.
$ads_asap_genre_strings = '';
// disclaimer below the ads. Make sure it contains the '%cachedate%' placeholder.
$ads_asap_disclaimer = 'Stand: %cachedate%<br>Preis &amp; Verfügbarkeit können sich geändert haben.';

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
        $ads_asap_disclaimer = 'As of %cachedate%<br>prices &amp; availability might be subject to change.';
        break;
    default  :
        $uselangs = array('de');
        break;
}

?>