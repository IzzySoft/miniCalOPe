<?php
#================================[ Directories ]===
// database file
$dbfile = dirname(__FILE__).'/metadata.db';
// Where your books are stored.
$bookroot    = 'books';
// Which book formats you want to use (file extensions). supported are epub and mobi
$bookformats = array('epub','mobi');
// file extension for book descriptions
$bookdesc_ext = array('desc');

#============================[ Web Service ]===
// Site Title
$sitetitle = 'Book Server';
// Full URL to miniCalOPe
$baseurl = 'http://localhost/opds/';
// Path relative to the web servers DOCUMENT_ROOT
$relurl  = '/opds/';
// how many items per page (currently ignored)
$perpage = 25;
// Full URL to the Wikipedia to use for author info
$wikibase= 'http://de.wikipedia.org/wiki/';

#============================[ Person Info ]===
// about you: Name, Homepage, Email
$owner   = 'John Doe';
$homepage= 'http://www.johndoe.com/';
$email   = 'john@johndoe.com';

#===========================[ stuff ]===
$debug = FALSE;

?>