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
// Setup templates
require_once('./lib/template.php');
switch($_REQUEST['pageformat']) {
    case 'html' : $pageformat = 'html'; break;
    default     : $pageformat = 'opds'; break;
}
$t = new Template("tpl/$pageformat");
// Setup translations
require_once('./lib/translation.php');
$transl = new translation(dirname(__FILE__).'/lang','en');
$transl->get_translations();
function trans($key,$m1="",$m2="",$m3="",$m4="",$m5="") {
  return $GLOBALS['transl']->transl($key,$m1,$m2,$m3,$m4,$m5);
}

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
    $tpl->set_var('last_update',trans('last_update'));
    $tpl->set_var('created_by',trans('created_by'));
    foreach(array('start_page','this_page','first_page','prev_page','next_page','last_page') as $page) $tpl->set_var($page,trans($page));
    foreach(array('sort_alpha','sort_bookcount','sort_date','sort_author') as $sort) $tpl->set_var($sort,trans($sort));
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

// We need the database
require_once('./lib/db_sqlite3.php');
$db = new DB_Sql();
$db->Database = $dbfile;

$db->query('SELECT COUNT(id) AS num FROM books');
$db->next_record();
$allbookcount = $db->f('num');

#========================================================[ Process request ]===
$prefix = req_word('default_prefix');
if ( empty($prefix) && empty($_REQUEST['action']) ) { // Startpage
    $t->set_file(array("template"=>"index.tpl"));
    set_basics($t);
    $t->set_var('author_list',trans('authors'));
    $t->set_var('title_list',trans('titles'));
    $t->set_var('tags_list',trans('tags'));
    $t->set_var('series_list',trans('series'));
    $t->set_var('num_allbooks',$allbookcount);
    if ($allbookcount==1) $t->set_var('allbooks','Buch');
    else $t->set_var('allbooks','Bücher');
    $t->pparse("out","template");
    exit;
}
$offset = req_int('offset');

switch($prefix) {
    //-----------------------------------------[ list of authors requested ]---
    case 'authors':
        $db->query('SELECT COUNT(id) AS num FROM authors');
        if ($db->next_record()) $num_authors = $db->f("num");
        else $num_authors = 0;
        $t->set_file(array("template"=>"authors.tpl"));
        $t->set_block('template','itemblock','item');
        $t->set_block('template','prevblock','prev');
        $t->set_block('template','nextblock','next');
        set_basics($t);
        $t->set_var('author_list',trans('authors'));
        $t->set_var('total',$num_authors);
        $t->set_var('per_page',$perpage);
        $t->set_var('start',1);
        $t->set_var('offset',$offset);
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title': $order = ' ORDER BY name'; $sortorder='title'; break;
            case 'books': $order = ' ORDER BY num DESC'; $sortorder='books'; break;
            default     : $order = '';
        }
        if ($num_authors>0) {
            $all = $db->lim_query('SELECT a.id id,a.name name,COUNT(b.id) num FROM authors a,books_authors_link ba, books b WHERE a.id=ba.author AND b.id=ba.book group by a.id'.$order, $offset, $perpage);
            $more = FALSE;
            $authors = array();
            while ( $db->next_record() ) {
                $num_books = $db->f('num');
                $t->set_var('name',$db->f('name'));
                $t->set_var('id',$db->f('id'));
                $t->set_var('num_books',$num_books);
                if ($num_books==1) $t->set_var('books',trans('book'));
                else $t->set_var('books',trans('books'));
                $t->parse('item','itemblock',$more);
                $more = TRUE;
            }
        }
        $t->set_var('sortorder',$sortorder);
        // pagination:
        $t->set_var('start',$offset +1); // offset for OPDS (1st entry)
        if ($offset==0) { // first page
            $t->set_var('icon1','2left_grey.png');
            $t->set_var('icon2','1left_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $t->set_var('poffset','0'); // OPDS only
            $t->parse('prev','prevblock');
        } else { // somewhere after
            $t->set_var('icon1','2left.png');
            $t->set_var('icon2','1left.png');
            $poff = max(0,$offset - $perpage);
            $t->set_var('poffset',$poff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=authors&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset=0&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=authors&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$poff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('prev','prevblock');
        }
        if ($all < $offset + $perpage) { // last page
            $t->set_var('icon1','1right_grey.png');
            $t->set_var('icon2','2right_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $noff = $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->parse('next','nextblock');
        } else { // somewhere before
            $t->set_var('icon1','1right.png');
            $t->set_var('icon2','2right.png');
            $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=authors&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$noff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=authors&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$loff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('next','nextblock');
        }
        $t->pparse("out","template");
        exit;
    //------------------------[ List of books for a given author requested ]---
    case 'author_id':
        $aid = req_int('query');
        $db->query('SELECT COUNT(id) AS num FROM books WHERE id IN (SELECT book FROM books_authors_link WHERE author='.$aid.')');
        if ($db->next_record()) $num_books = $db->f("num");
        else $num_books = 0;
        $t->set_file(array("template"=>"author.tpl"));
        $t->set_block('template','itemblock','item');
        $t->set_block('template','prevblock','prev');
        $t->set_block('template','nextblock','next');
        set_basics($t);
        $t->set_var('back_to_authors',trans('back_to_authors'));
        $t->set_var('aid',$aid);
        $t->set_var('wikibase',$wikibase);
        $db->query('SELECT name FROM authors WHERE id='.$aid);
        $db->next_record();
        $t->set_var('wikiauthor',str_replace(' ','_',$db->f('name')));
        $t->set_var('books_by_whom',trans('books_by_whom',$db->f('name')));
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title': $order = ' ORDER BY title'; $sortorder='title'; break;
            case 'date' : $order = ' ORDER BY timestamp'; $sortorder='date'; break;
            default     : $order = '';
        }
        $all = $db->lim_query('SELECT id,title,isbn,timestamp FROM books WHERE id IN (SELECT book FROM books_authors_link WHERE author='.$aid.')'.$order, $offset, $perpage);
        $more = FALSE;
        while ( $db->next_record() ) {
            $t->set_var('bid',$db->f('id'));
            $t->set_var('title',$db->f('title'));
            $t->set_var('isbn',$db->f('isbn'));
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        // pagination:
        $t->set_var('start',$offset +1); // offset for OPDS (1st entry)
        $t->set_var('sortorder',$sortorder);
        $t->set_var('total',$all);
        $t->set_var('per_page',$perpage);
        $t->set_var('offset',$offset);
        $t->set_var('start',$offset +1);
        if ($offset==0) { // first page
            $t->set_var('icon1','2left_grey.png');
            $t->set_var('icon2','1left_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $t->set_var('poffset','0'); // OPDS only
            $t->parse('prev','prevblock');
        } else { // somewhere after
            $t->set_var('icon1','2left.png');
            $t->set_var('icon2','1left.png');
            $poff = max(0,$offset - $perpage);
            $t->set_var('poffset',$poff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=author_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;query='.$aid.'&amp;offset=0&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=author_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$poff.'&amp;query='.$aid.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('prev','prevblock');
        }
        if ($all < $offset + $perpage) { // last page
            $t->set_var('icon1','1right_grey.png');
            $t->set_var('icon2','2right_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $noff = $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->parse('next','nextblock');
        } else { // somewhere before
            $t->set_var('icon1','1right.png');
            $t->set_var('icon2','2right.png');
            $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=author_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$noff.'&amp;query='.$aid.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=author_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$loff.'&amp;query='.$aid.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('next','nextblock');
        }
        $t->pparse("out","template");
        exit;
    //------------------------------[ List of all books by title requested ]---
    case 'titles':
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title': $order = ' ORDER BY b.title'; $sortorder='title'; break;
            case 'name' : $order = ' ORDER BY a.name'; $sortorder='name'; break;
            case 'time' : $order = ' ORDER BY b.timestamp'; $sortorder='time'; break;
            default     : $order = ''; $sortorder=''; break;
        }
        $all = $db->lim_query('SELECT b.id,b.title,b.isbn,a.name,b.timestamp FROM books b,books_authors_link bl,authors a WHERE b.id=bl.book and a.id=bl.author '.$order, $offset, $perpage);
        $t->set_file(array("template"=>"titles.tpl"));
        $t->set_block('template','itemblock','item');
        $t->set_block('template','prevblock','prev');
        $t->set_block('template','nextblock','next');
        set_basics($t);
        $t->set_var('title_list',trans('titles'));
        $t->set_var('offset',$offset);
        $t->set_var('per_page',$perpage);
        $t->set_var('total',$all); // OPDS only
        $t->set_var('num_allbooks',$allbookcount);
        if ($allbookcount==1) $t->set_var('allbooks',trans('book'));
        else $t->set_var('allbooks',trans('books'));
        // pagination:
        $t->set_var('start',$offset +1); // offset for OPDS (1st entry)
        $t->set_var('sortorder',$sortorder);
        if ($offset==0) { // first page
            $t->set_var('icon1','2left_grey.png');
            $t->set_var('icon2','1left_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $t->set_var('poffset','0'); // OPDS only
            $t->parse('prev','prevblock');
        } else { // somewhere after
            $t->set_var('icon1','2left.png');
            $t->set_var('icon2','1left.png');
            $poff = max(0,$offset - $perpage);
            $t->set_var('poffset',$poff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=titles&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset=0&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=titles&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sort_order.'&amp;offset='.$poff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('prev','prevblock');
        }
        if ($all < $offset + $perpage) { // last page
            $t->set_var('icon1','1right_grey.png');
            $t->set_var('icon2','2right_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $noff = $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->parse('next','nextblock');
        } else { // somewhere before
            $t->set_var('icon1','1right.png');
            $t->set_var('icon2','2right.png');
            $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=titles&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$noff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=titles&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$loff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('next','nextblock');
        }
        // records:
        $more = FALSE;
        while ( $db->next_record() ) {
            $t->set_var('bid',$db->f('id'));
            $t->set_var('title',$db->f('title') .' von '. $db->f('name'));
            $t->set_var('isbn',$db->f('isbn'));
            $t->set_var('pubdate',str_replace(' ','T',$db->f('timestamp')).$timezone);
            $t->set_var('pubdate_human', $db->f('timestamp'));
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        $t->set_var('pubdate',str_replace(' ','T',$pubdate).$timezone);
        $t->pparse("out","template");
        exit;
    //-------------------------------[ List of all books by tags requested ]---
    case 'tags':
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title': $order = ' ORDER BY name'; $sortorder='title'; break;
            case 'books': $order = ' ORDER BY num DESC'; $sortorder='books'; break;
            default     : $order = '';
        }
        $all = $db->lim_query('SELECT t.id id,t.name name, count(b.id) num FROM tags t,books_tags_link bt, books b WHERE bt.book=b.id and bt.tag = t.id GROUP BY t.id'.$order, $offset, $perpage);
        $t->set_file(array("template"=>"tags.tpl"));
        $t->set_block('template','itemblock','item');
        $t->set_block('template','prevblock','prev');
        $t->set_block('template','nextblock','next');
        set_basics($t);
        $t->set_var('tags_list',trans('tags'));
        $t->set_var('num_allbooks',$allbookcount);
        if ($allbookcount==1) $t->set_var('allbooks',trans('book'));
        else $t->set_var('allbooks',trans('books'));
        $more = FALSE;
        #id,name,num_books,books
        while ( $db->next_record() ) {
            $tag['num_books'] = $db->f('num');
            $t->set_var('id',$db->f('id'));
            $t->set_var('name',$db->f('name'));
            $t->set_var('num_books',$tag['num_books']);
            if ($tag['num_books']==1) $t->set_var('books',trans('book'));
            else $t->set_var('books',trans('books'));
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        // pagination:
        $t->set_var('start',$offset +1); // offset for OPDS (1st entry)
        $t->set_var('sortorder',$sortorder);
        $t->set_var('total',$all);
        $t->set_var('per_page',$perpage);
        $t->set_var('offset',$offset);
        $t->set_var('start',$offset +1);
        if ($offset==0) { // first page
            $t->set_var('icon1','2left_grey.png');
            $t->set_var('icon2','1left_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $t->set_var('poffset','0'); // OPDS only
            $t->parse('prev','prevblock');
        } else { // somewhere after
            $t->set_var('icon1','2left.png');
            $t->set_var('icon2','1left.png');
            $poff = max(0,$offset - $perpage);
            $t->set_var('poffset',$poff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tags&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;query='.$aid.'&amp;offset=0&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tags&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$poff.'&amp;query='.$aid.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('prev','prevblock');
        }
        if ($all < $offset + $perpage) { // last page
            $t->set_var('icon1','1right_grey.png');
            $t->set_var('icon2','2right_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $noff = $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->parse('next','nextblock');
        } else { // somewhere before
            $t->set_var('icon1','1right.png');
            $t->set_var('icon2','2right.png');
            $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tags&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$noff.'&amp;query='.$aid.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tags&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$loff.'&amp;query='.$aid.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('next','nextblock');
        }
        $t->pparse("out","template");
        exit;
    //---------------------------[ List of books for a given tag requested ]---
    case 'tag_id':
        $t->set_file(array("template"=>"tag.tpl"));
        $t->set_block('template','itemblock','item');
        $t->set_block('template','prevblock','prev');
        $t->set_block('template','nextblock','next');
        set_basics($t);
        $t->set_var('tags_list',trans('tags'));
        $tag_id = req_int('query');
        $db->query('SELECT name FROM tags WHERE id='.$tag_id);
        $db->next_record();
        $t->set_var('books_with_tag',trans('books_with_tag',$db->f('name')));
        $t->set_var('aid',$tag_id);
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title' : $order = ' ORDER BY b.title'; $sortorder = 'title'; break;
            case 'author': $order = ' ORDER BY a.name'; $sortorder = 'author'; break;
            default     : $order = '';
        }
        $all = $db->lim_query('SELECT b.id,b.title,b.isbn,a.name FROM books b,authors a, books_authors_link l WHERE b.id=l.book AND a.id=l.author AND b.id IN (SELECT book FROM books_tags_link WHERE tag='.$tag_id.')'.$order, $offset, $perpage);
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
            $t->set_var('title_by_author',trans('title_by_author',$book['title'],$book['author']));
            $t->set_var('isbn',$book['isbn']);
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        // pagination:
        $t->set_var('start',$offset +1); // offset for OPDS (1st entry)
        $t->set_var('sortorder',$sortorder);
        $t->set_var('total',$all);
        $t->set_var('per_page',$perpage);
        $t->set_var('offset',$offset);
        $t->set_var('start',$offset +1);
        if ($offset==0) { // first page
            $t->set_var('icon1','2left_grey.png');
            $t->set_var('icon2','1left_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $t->set_var('poffset','0'); // OPDS only
            $t->parse('prev','prevblock');
        } else { // somewhere after
            $t->set_var('icon1','2left.png');
            $t->set_var('icon2','1left.png');
            $poff = max(0,$offset - $perpage);
            $t->set_var('poffset',$poff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tag_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;query='.$tag_id.'&amp;offset=0&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tag_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$poff.'&amp;query='.$tag_id.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('prev','prevblock');
        }
        if ($all < $offset + $perpage) { // last page
            $t->set_var('icon1','1right_grey.png');
            $t->set_var('icon2','2right_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $noff = $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->parse('next','nextblock');
        } else { // somewhere before
            $t->set_var('icon1','1right.png');
            $t->set_var('icon2','2right.png');
            $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tag_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$noff.'&amp;query='.$tag_id.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=tag_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$loff.'&amp;query='.$tag_id.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('next','nextblock');
        }
        $t->pparse("out","template");
        exit;
    //----------------------------------------------------[ List of series ]---
    case 'series':
        $t->set_file(array("template"=>"series.tpl"));
        $t->set_block('template','itemblock','item');
        $t->set_block('template','prevblock','prev');
        $t->set_block('template','nextblock','next');
        set_basics($t);
        $t->set_var('series_list',trans('series'));
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title': $order = ' ORDER BY name'; $sortorder='title'; break;
            case 'books': $order = ' ORDER BY num DESC'; $sortorder='books'; break;
            default     : $order = '';
        }
        $all = $db->lim_query('SELECT s.id id,s.name name, count(b.id) num FROM series s, books b, books_series_link bs WHERE bs.book=b.id AND bs.series=s.id GROUP BY s.id'.$order, $offset, $perpage);
        $t->set_var('num_allbooks',$allbookcount);
        if ($allbookcount==1) $t->set_var('allbooks',trans('book'));
        else $t->set_var('allbooks',trans('books'));
        $more = FALSE;
        #id,name,num_books,books
        while ( $db->next_record() ) {
            $tag['num_books'] = $db->f('num');
            $t->set_var('id',$db->f('id'));
            $t->set_var('name',$db->f('name'));
            $t->set_var('num_books',$tag['num_books']);
            if ($tag['num_books']==1) $t->set_var('books',trans('book'));
            else $t->set_var('books',trans('books'));
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        // pagination:
        $t->set_var('start',$offset +1); // offset for OPDS (1st entry)
        $t->set_var('sortorder',$sortorder);
        $t->set_var('total',$all);
        $t->set_var('per_page',$perpage);
        $t->set_var('offset',$offset);
        $t->set_var('start',$offset +1);
        if ($offset==0) { // first page
            $t->set_var('icon1','2left_grey.png');
            $t->set_var('icon2','1left_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $t->set_var('poffset','0'); // OPDS only
            $t->parse('prev','prevblock');
        } else { // somewhere after
            $t->set_var('icon1','2left.png');
            $t->set_var('icon2','1left.png');
            $poff = max(0,$offset - $perpage);
            $t->set_var('poffset',$poff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset=0&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$poff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('prev','prevblock');
        }
        if ($all <= $offset + $perpage) { // last page
            $t->set_var('icon1','1right_grey.png');
            $t->set_var('icon2','2right_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $noff = $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->parse('next','nextblock');
        } else { // somewhere before
            $t->set_var('icon1','1right.png');
            $t->set_var('icon2','2right.png');
            $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$noff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$loff.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('next','nextblock');
        }
        $t->pparse("out","template");
        exit;
    //-------------------------[ List of books for a given serie requested ]---
    case 'series_id':
        $t->set_file(array("template"=>"serie.tpl"));
        $t->set_block('template','itemblock','item');
        $t->set_block('template','prevblock','prev');
        $t->set_block('template','nextblock','next');
        set_basics($t);
        $t->set_var('back_to_series',trans('back_to_series'));
        $t->set_var('sort_index',trans('sort_index'));
        $series_id = req_int('query');
        $db->query('SELECT name FROM series WHERE id='.$series_id);
        $db->next_record();
        $t->set_var('books_in_serie',trans('books_in_serie',$db->f('name')));
        $t->set_var('aid',$series_id);
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title' : $order = ' ORDER BY b.title'; $sortorder = 'title'; break;
            case 'author': $order = ' ORDER BY a.name'; $sortorder = 'author'; break;
            case 'index' : $order = ' ORDER BY b.series_index'; $sortorder = 'index'; break;
            default     : $order = '';
        }
        $all = $db->lim_query('SELECT b.id id,b.title title,b.isbn isbn,b.series_index series_index, a.name name from books b, books_series_link bs, books_authors_link ba, authors a WHERE bs.series='.$series_id.' AND bs.book=b.id AND ba.book=b.id AND ba.author=a.id'.$order, $offset, $perpage);
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
            $t->set_var('title_by_author',trans('title_by_author',$book['title'],$book['author']));
            $t->set_var('isbn',$book['isbn']);
            $t->parse('item','itemblock',$more);
            $more = TRUE;
        }
        // pagination:
        $t->set_var('start',$offset +1); // offset for OPDS (1st entry)
        $t->set_var('sortorder',$sortorder);
        $t->set_var('total',$all);
        $t->set_var('per_page',$perpage);
        $t->set_var('offset',$offset);
        $t->set_var('start',$offset +1);
        if ($offset==0) { // first page
            $t->set_var('icon1','2left_grey.png');
            $t->set_var('icon2','1left_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $t->set_var('poffset','0'); // OPDS only
            $t->parse('prev','prevblock');
        } else { // somewhere after
            $t->set_var('icon1','2left.png');
            $t->set_var('icon2','1left.png');
            $poff = max(0,$offset - $perpage);
            $t->set_var('poffset',$poff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;query='.$series_id.'&amp;offset=0&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$poff.'&amp;query='.$tag_id.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('prev','prevblock');
        }
        if ($all < $offset + $perpage) { // last page
            $t->set_var('icon1','1right_grey.png');
            $t->set_var('icon2','2right_grey.png');
            $t->set_var('link1_open','');
            $t->set_var('link2_open','');
            $t->set_var('link_close','');
            $noff = $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->parse('next','nextblock');
        } else { // somewhere before
            $t->set_var('icon1','1right.png');
            $t->set_var('icon2','2right.png');
            $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
            $t->set_var('noffset',$noff); // OPDS only
            $t->set_var('loffset',$loff); // OPDS only
            $t->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$noff.'&amp;query='.$series_id.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].'?default_prefix=series_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;offset='.$loff.'&amp;query='.$series_id.'&amp;pageformat='.$pageformat.'">');
            $t->set_var('link_close','</A>');
            $t->parse('next','nextblock');
        }
        $t->pparse("out","template");
        exit;
    //----------------------------------------------[ Handle a single book ]---
    case '':
        // Display book details
        if (isset($_REQUEST['action']) && $_REQUEST['action']=='bookdetails') {
            $bookid = req_int('book');
            $db->query("SELECT name FROM authors WHERE id IN (SELECT author FROM books_authors_link WHERE book=$bookid)");
            $author = '';
            while ( $db->next_record() ) $author .= ', ' . $db->f('name');
            $author = substr($author,2);
            $db->query("SELECT title,isbn,series_index,strftime('%Y-%m-%dT%H:%M:%S',timestamp) pubdate,uri FROM books WHERE id=".$bookid);
            $db->next_record();
            $book = array(
              'title'=>$db->f('title'), 'isbn'=>$db->f('isbn'), 'tags'=>'', 'series_index'=>$db->f('series_index'),
              'uri'=>$db->f('uri'), 'author'=>$author,
              'pubdate'=>$db->f('pubdate').$timezone, 'pubdate_human'=>date("d-m-Y H:i",strtotime($db->f('pubdate')))
            );
            $db->query("SELECT name FROM tags WHERE id IN (SELECT tag FROM books_tags_link WHERE book=$bookid)");
            while ( $db->next_record() ) $book['tags'] .= ', '.$db->f('name');
            if (strlen($book['tags'])) $book['tags'] = substr($book['tags'],2);
            $db->query("SELECT name FROM series WHERE id IN (SELECT series FROM books_series_link WHERE book=$bookid)");
            $db->next_record();
            $book['series'] = $db->f('name');
            if (!empty($book['series'])) $book['series'] .= ' (#'.$book['series_index'].')';
            $db->query("SELECT name FROM publishers WHERE id IN (SELECT publisher FROM books_publishers_link WHERE book=$bookid)");
            $db->next_record();
            $book['publisher'] = $db->f('name');
            $db->query("SELECT text FROM comments WHERE book=$bookid");
            $db->next_record();
            $book['comment'] = htmlentities($db->f('text'));
            $db->query("SELECT r.rating FROM ratings r, books_ratings_link br WHERE br.book=$bookid AND r.id=br.rating");
            if ( $db->next_record() ) $book['rating'] = $db->f('rating'); else $book['rating'] = 0;
            $files = get_filenames($db,$bookid);
            $t->set_file(array("template"=>"book.tpl"));
            $t->set_block('template','datablock','data');
            $t->set_block('template','itemblock','item');
            $t->set_block('template','coverblock','cover');
            set_basics($t);
            $t->set_var('back_to_authors',trans('back_to_authors'));
            $t->set_var('data_name',trans('title'));
            $t->set_var('data_data',$book['title']);
            $t->parse('data','datablock');
            foreach (array('author','isbn','tags','series','publisher','uri','rating') as $field) {
                if ( empty($book[$field]) ) continue;
                if ($field=='series') $t->set_var('data_name',trans('serie'));
                else $t->set_var('data_name',trans($field));
                if ($field=='uri') $t->set_var('data_data',"<A HREF='".$book[$field]."'>".$book[$field]."</A>");
                else $t->set_var('data_data',$book[$field]);
                $t->parse('data','datablock',TRUE);
            }
            if ( !empty($book['rating']) ) {
                $t->set_var('data_name',trans('rating'));
                $t->set_var('data_data','<IMG SRC="'.$relurl.'tpl/icons/rating_'.$book['rating'].'.gif" ALT="Rating '.$book['rating'].'">');
                $t->parse('data','datablock',TRUE);
            }
            $t->set_var("field_download",trans('download'));
            $t->set_var('id',$bookid);
            $t->set_var('title_by_author',trans('title_by_author',$book['title'],$author));
            $t->set_var('field_comment',trans('comment'));
            if ( empty($book['comment']) ) {
                $t->set_var('comment',lang('not_available'));
            } else {
                $t->set_var('comment',nl2br(html_entity_decode($book['comment'])));
            }
            $t->set_var('pubdate',$book['pubdate']);
            $t->set_var('pubdate_human',$book['pubdate_human']);
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
                $coverimg = $file['path'].DIRECTORY_SEPARATOR.substr($file['name'],0,strrpos($file['name'],'.')).'.jpg';
                $t->set_var('flength',$file['size']);
                $t->set_var('flength_human',number_format(round($file['size']/1024)).'kB');
                $t->set_var('format',$file['format']);
                $t->parse('item','itemblock',$more);
                $more = TRUE;
            }
            if ($use_lang=='cal') $coverimg = $cover_base.DIRECTORY_SEPARATOR.$use_lang.DIRECTORY_SEPARATOR.$bookid.'.jpg';
            $cover_type = 'jpeg';
            if ( !file_exists($coverimg) ) {
              $coverimg = preg_replace('!jpg$!','png',$coverimg);
              $cover_type = 'png';
            }
            if ( file_exists($coverimg) && is_readable($coverimg) ) {
                $t->set_var('cover_type',$cover_type);
                $t->set_var('cover_src',$coverimg);
                $t->set_var('cover_width',$cover_width);
                $t->parse('cover','coverblock');
            }
            $t->pparse("out","template");
            exit;
        // Send the requested book for download
        } elseif (isset($_REQUEST['action']) && $_REQUEST['action']=='getbook') { // bookid=req[book], req[format] = epub/mobi
            $files = get_filenames($db,req_int('book'),req_word('format'));
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