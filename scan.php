<?
require_once('./config.php');
require_once('./lib/common.php');
require_once('./lib/files.php');
require_once('./lib/db_sqlite3.php');
require_once('./lib/db.php');
require_once('./lib/template.php');

$tpl = new Template('tpl');
$db = new db($dbfile);

$pubdate = date('c');
$books = array();
$genres = array(); $allGenres = array();
$authors = array();

// directory structure for books directory:
// <lang>/<genre>/<author>/<books>

#===========================================================[ Collect data ]===
// Go for the languages available
debugOut("Scanning $bookroot");
$langs = scanFolder($bookroot);

// Now collect the genres
foreach($langs as $lang) {
    debugOut("* Scanning langDir '$lang'");
    $genres[$lang] = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang);
    $allGenres     = array_merge($allGenres,$genres[$lang]);

    // Now come the authors
    foreach($genres[$lang] as $genre) {
        debugOut("  + Scanning genre '$genre'");
        $tauthors = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $genre);
        $authors = array_merge( $authors, $tauthors);

        // Guess what - they wrote books!
        foreach($tauthors as $author) {
            debugOut("    - Scanning author '$author'");
            $tbooks = scanFolder($bookroot . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $genre . DIRECTORY_SEPARATOR . $author, 'files');
            //array[name] with [files][ext] and [desc]
            foreach($tbooks as $book=>$dummy) {
              $tbooks[$book]['lang']   = $lang;
              $tbooks[$book]['genre']  = $genre;
              $tbooks[$book]['author'] = $author;
            }
            $books = array_merge($books,$tbooks);
        }
    }
}
sort($allGenres);
$allGenres = array_unique($allGenres);
$authors = array_unique($authors);

debugOut( "Authors: ".implode(', ',$authors) );
#======================================================[ Feed the database ]===
$db->truncAll();
$db->make_genres($allGenres);
$db->make_authors($authors);
$db->make_books($books);

exit;

?>