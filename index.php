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

require_once('./config.php');
require_once('./lib/files.php');
require_once('./lib/template.php');
switch($_REQUEST['pageformat']) {
    case 'html' : $pageformat = 'html'; break;
    default     : $pageformat = 'opds'; break;
}
$t = new Template("tpl/$pageformat");

$pubdate = date('c',filemtime($dbfile));
$pubdate_human = date('Y-m-d H:i',filemtime($dbfile));

#================================================================[ Helpers ]===
/** Setup common template values
 * @function set_basics
 * @param object tpl template object
 */
function set_basics(&$tpl) {
    foreach(array('owner','homepage','email','pubdate','pubdate_human','baseurl','relurl') as $item) $tpl->set_var($item,$GLOBALS[$item]);
    $tpl->set_var('lang',$GLOBALS['use_lang']);
    $tpl->set_var('site_title',$GLOBALS['sitetitle']);
}

/** Obtain information on available files for a given book
 * @function get_filenames
 * @param object db database object for Calibre DB
 * @param integer bookid ID of the book to obtain the file infor for
 * @param optional string format get information just for the given format. If not set, get all formats
 * @return array files [0..n] of array(name,size,format,path)
 */
function get_filenames(&$db,$bookid,$format='') {
    $db->query("SELECT title,path FROM books WHERE id=$bookid");
    $db->next_record();
    $path = $GLOBALS['bookroot'].$db->f('path');
    $dir = dir($path);
    $files = array();
    while (false !== $file = $dir->read()) {
        if (strlen($file)<=5) continue;
        $pos  = strrpos($file,'.');
        $suff = substr($file,$pos+1);
        if (!empty($format) && $suff!=$format) continue;
        // in non-calibre mode (2-digit lang) different books of the author may be in the same dir:
        if ( strlen($GLOBALS['use_lang'])==2 && substr($file,0,$pos)!=$db->f('title') ) continue;
        if (in_array($suff,$GLOBALS['bookformats'])) $files[] = array('name'=>$file,'size'=>filesize("$path/$file"),'format'=>$suff,'path'=>$path);
    }
    return $files;
}

#========================================================[ Process request ]===
if (!empty($_REQUEST['default_prefix'])) $prefix = $_REQUEST['default_prefix'];
elseif (empty($_REQUEST['action'])) { // Startpage
    $t->set_file(array("template"=>"index.tpl"));
    set_basics($t);
    $t->pparse("out","template");
    exit;
}

// This is no longer the start page -- so from here on we need the database
require_once('./lib/db_sqlite3.php');
$db = new DB_Sql();
$db->Database = $dbfile;

switch($prefix) {
    //-----------------------------------------[ list of authors requested ]---
    case 'authors':
        $db->query('SELECT COUNT(id) AS num FROM authors');
        if ($db->next_record()) $num_authors = $db->f("num");
        else $num_authors = 0;
        $t->set_file(array("template"=>"authors.tpl"));
        $t->set_block('template','itemblock','item');
        set_basics($t);
        $t->set_var('total',$num_authors);
        $t->set_var('per_page',$perpage);
        $t->set_var('start',1);
        switch($_REQUEST['sort_order']) {
            case 'title': $order = ' ORDER BY name'; break;
            default     : $order = '';
        }
        if ($num_authors>0) {
            $db->query('SELECT id,name FROM authors'.$order);
            $more = FALSE;
            $authors = array();
            while ( $db->next_record() ) $authors[] = array('name'=>$db->f('name'),'id'=>$db->f('id'));
            foreach ($authors as $author) {
                $db->query('SELECT COUNT(id) books FROM books_authors_link WHERE author='.$author['id']);
                $db->next_record();
                $num_books = $db->f('books');
                $t->set_var('name',$author['name']);
                $t->set_var('id',$author['id']);
                $t->set_var('num_books',$num_books);
                if ($num_books==1) $t->set_var('books','Buch');
                else $t->set_var('books','Bücher');
                $t->parse('item','itemblock',$more);
                $more = TRUE;
            }
        }
        $t->pparse("out","template");
        exit;
    //------------------------[ List of books for a given author requested ]---
    case 'author_id':
        $db->query('SELECT COUNT(id) AS num FROM books WHERE id IN (SELECT book FROM books_authors_link WHERE author='.$_REQUEST['query'].')');
        if ($db->next_record()) $num_books = $db->f("num");
        else $num_books = 0;
        $t->set_file(array("template"=>"author.tpl"));
        $t->set_block('template','itemblock','item');
        set_basics($t);
        $t->set_var('total',$num_books);
        $t->set_var('per_page',$perpage);
        $t->set_var('start',1);
        $t->set_var('aid',$REQUEST['query']);
        $t->set_var('wikibase',$wikibase);
        $db->query('SELECT name FROM authors WHERE id='.$_REQUEST['query']);
        $db->next_record();
        $t->set_var('wikiauthor',str_replace(' ','_',$db->f('name')));
        $t->set_var('author_name',$db->f('name'));
        $db->query('SELECT id,title,isbn FROM books WHERE id IN (SELECT book FROM books_authors_link WHERE author='.$_REQUEST['query'].')');
        $more = FALSE;
        while ( $db->next_record() ) {
            $t->set_var('bid',$db->f('id'));
            $t->set_var('title',$db->f('title'));
            $t->set_var('isbn',$db->f('isbn'));
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        $t->pparse("out","template");
        exit;
    //------------------------------[ List of all books by title requested ]---
    case 'titles':
        switch($_REQUEST['sort_order']) {
            case 'title': $order = ' ORDER BY title'; break;
            default     : $order = '';
        }
        $db->query('SELECT id,title,isbn FROM books'.$order);
        $t->set_file(array("template"=>"titles.tpl"));
        $t->set_block('template','itemblock','item');
        set_basics($t);
        $t->set_var('total',$num_books);
        $t->set_var('per_page',$perpage);
        $t->set_var('start',1);
        $more = FALSE;
        while ( $db->next_record() ) {
            $t->set_var('bid',$db->f('id'));
            $t->set_var('title',$db->f('title'));
            $t->set_var('isbn',$db->f('isbn'));
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        $t->pparse("out","template");
        exit;
    //-------------------------------[ List of all books by tags requested ]---
    case 'tags':
        switch($_REQUEST['sort_order']) {
            case 'title': $order = ' ORDER BY name'; break;
            default     : $order = '';
        }
        $db->query('SELECT id,name FROM tags'.$order);
        $tags = array();
        while ( $db->next_record() ) $tags[] = array('id'=>$db->f('id'),'name'=>$db->f('name'));
        $t->set_file(array("template"=>"tags.tpl"));
        $t->set_block('template','itemblock','item');
        set_basics($t);
        $t->set_var('total',$num_books);
        $t->set_var('per_page',$perpage);
        $t->set_var('start',1);
        $more = FALSE;
        #id,name,num_books,books
        foreach ( $tags as $tag ) {
            $db->query('SELECT count(id) num_books FROM books WHERE id IN (SELECT book FROM books_tags_link WHERE tag='.$tag['id'].')');
            $db->next_record();
            $t->set_var('id',$tag['id']);
            $t->set_var('name',$tag['name']);
            $t->set_var('num_books',$db->f('num_books'));
            if ($tag['num_books']==1) $t->set_var('books','Buch');
            else $t->set_var('books','Bücher');
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        $t->pparse("out","template");
        exit;
    //---------------------------[ List of books for a given tag requested ]---
    case 'tag_id':
        $t->set_file(array("template"=>"tag.tpl"));
        $t->set_block('template','itemblock','item');
        set_basics($t);
        if (is_numeric($_REQUEST['query'])) $tag_id = $_REQUEST['query'];
        else $tag_id = 0;
        $db->query('SELECT name FROM tags WHERE id='.$tag_id);
        $db->next_record();
        $t->set_var('tag_name',$db->f('name'));
        $t->set_var('total',$num_books);
        $t->set_var('per_page',$perpage);
        $t->set_var('aid',$tag_id);
        $t->set_var('start',1);
        switch($_REQUEST['sort_order']) {
            case 'title' : $order = ' ORDER BY b.title'; break;
            case 'author': $order = ' ORDER BY a.name'; break;
            default     : $order = '';
        }
        $db->query('SELECT b.id,b.title,b.isbn,a.name FROM books b,authors a, books_authors_link l WHERE b.id=l.book AND a.id=l.author AND b.id IN (SELECT book FROM books_tags_link WHERE tag='.$_REQUEST['query'].')'.$order);
        $books = array();
        while ( $db->next_record() ) {
            $bid = $db->f('id');
            if ( isset($books[$bid]) ) {
                $books[$bid]['author'] .= ', '.$db->f('name');
            } else {
                $books[$bid] = array('id'=>$bid,'title'=>$db->f('title'),'isbn'=>$db->f('isbn'),'author'=>$db->f('name'));
            }
        }
        $more = FALSE;
        foreach ( $books as $book ) {
            $t->set_var('bid',$book['id']);
            $t->set_var('title',$book['title']);
            $t->set_var('author',$book['author']);
            $t->set_var('isbn',$book['isbn']);
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        $t->pparse("out","template");
        exit;
    //----------------------------------------------[ Handle a single book ]---
    case '':
        // Display book details
        if (isset($_REQUEST['action']) && $_REQUEST['action']=='bookdetails') {
            $bookid = $_REQUEST['book'];
            $db->query("SELECT author FROM books_authors_link WHERE book=$bookid");
            $db->next_record();
            $db->query('SELECT name FROM authors WHERE id='.$db->f('author'));
            $db->next_record();
            $author = $db->f('name');
            $db->query("SELECT title,isbn,series_index,strftime('%Y-%m-%dT%H:%M:%S',timestamp) pubdate FROM books WHERE id=".$_REQUEST['book']);
            $db->next_record();
            $book = array(
              'title'=>$db->f('title'), 'isbn'=>$db->f('isbn'), 'tags'=>'', 'series_index'=>$db->f('series_index'),
              'pubdate'=>$db->f('pubdate').$timezone, 'pubdate_human'=>date("d-m-Y H:i",strtotime($db->f('pubdate')))
            );
            $db->query("SELECT name FROM tags WHERE id IN (SELECT tag FROM books_tags_link WHERE book=$bookid)");
            while ( $db->next_record() ) $book['tags'] .= ', '.$db->f('name');
            if (strlen($book['tags'])) $book['tags'] = substr($book['tags'],2);
            $db->query("SELECT name FROM series WHERE id IN (SELECT series FROM books_series_link WHERE book=$bookid)");
            $db->next_record();
            $book['series'] = $db->f('name').' (#'.$book['series_index'].')';
            $db->query("SELECT name FROM publishers WHERE id IN (SELECT publisher FROM books_publishers_link WHERE book=$bookid)");
            $db->next_record();
            $book['publisher'] = $db->f('name');
            $db->query("SELECT text FROM comments WHERE book=$bookid");
            $db->next_record();
            $book['comment'] = htmlentities($db->f('text'));
            $files = get_filenames($db,$bookid);
            $t->set_file(array("template"=>"book.tpl"));
            $t->set_block('template','itemblock','item');
            $t->set_block('template','coverblock','cover');
            set_basics($t);
            $t->set_var('id',$bookid);
            $t->set_var('author',$author);
            $t->set_var('title',$book['title']);
            $t->set_var('tags',$book['tags']);
            $t->set_var('series',$book['series']);
            $t->set_var('publisher',$book['publisher']);
            $t->set_var('isbn',$book['isbn']);
            $t->set_var('comment',nl2br(html_entity_decode($book['comment'])));
            $t->set_var('pubdate',$book['pubdate']);
            $t->set_var('pubdate_human',$book['pubdate_human']);
            $coverimg = $cover_base.DIRECTORY_SEPARATOR.$use_lang.DIRECTORY_SEPARATOR.$bookid.'.jpg';
            if ( file_exists($coverimg) && is_readable($coverimg) ) {
                $t->set_var('cover_src',$coverimg);
                $t->set_var('cover_width',$cover_width);
                $t->parse('cover','coverblock');
            }
            $more = FALSE;
            foreach ($files as $file) {
                switch($file['format']) {
                    case 'epub':
                       $t->set_var('ftype','epub+zip');
                       $t->set_var('ftype_human','ePub');
                       $t->set_var('ftitle','EPUB');
                       break;
                    case 'mobi':
                       $t->set_var('ftype','x-mobipocket-ebook');
                       $t->set_var('ftype_human','MobiPocket');
                       $t->set_var('ftitle','Kindle');
                      break;
                    default: break;
                }
                $t->set_var('flength',$file['size']);
                $t->set_var('flength_human',number_format(round($file['size']/1024)).'kB');
                $t->set_var('format',$file['format']);
                $t->parse('item','itemblock',$more);
                $more = TRUE;
            }
            $t->pparse("out","template");
            exit;
        // Send the requested book for download
        } elseif (isset($_REQUEST['action']) && $_REQUEST['action']=='getbook') { // bookid=req[book], req[format] = epub/mobi
            $files = get_filenames($db,$_REQUEST['book'],$_REQUEST['format']);
            $book  = $files[0]['path'].'/'.$files[0]['name'];
            if ($fd = fopen ($book,"rb")) {
                logg($book,'DOWNLOAD');
                switch($files[0]['format']) {
                    case 'epub': header("Content-type: application/epub+zip"); break;
                    case 'mobi': header("Content-type: application/x-mobipocket-ebook"); break;
                }
                header("Content-Disposition: attachment; filename=\"".$files[0]['name']."\"");
                header("Content-length: ".$files[0]['size']);
                fpassthru($fd);
                fclose($fd);
                exit;
            }
            exit;
        }
}
?>