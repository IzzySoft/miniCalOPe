<?php
#############################################################################
# miniCalOPe                               (c) 2010-2015 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
#############################################################################
# $Id$

require_once('./lib/class.logging.php'); // must come first as it also defines some CONST
require_once('./config.php');
require_once('./lib/common.php');
require_once('./lib/class.filefuncs.php');
$filefuncs = new filefuncs($logger,$use_markdown,$bookformats,$bookdesc_ext,$bookmeta_ext,$check_xml,$skip_broken_xml);

// Setup templates
require_once('./lib/class.template.php');
$pageformat = req_word('pageformat');
switch($pageformat) {
    case 'html' : $pageformat = 'html'; break;
    default     : $pageformat = 'opds'; break;
}
$t = new Template("tpl/$pageformat");
// Setup translations
require_once('./lib/class.translation.php');
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
    $db->query("SELECT path FROM books WHERE id=$bookid");
    $db->next_record();
    $path = $GLOBALS['bookroot'].$db->f('path');
    $query = "SELECT format,uncompressed_size,name FROM data WHERE book=$bookid";
    if (!empty($format)) $query .= " AND format='$format'";
    $db->query($query);
    $files = array();
    while ($db->next_record()) {
      $files[] = array('name'=>$db->f('name'),'size'=>$db->f('uncompressed_size'),'format'=>$db->f('format'),'path'=>$path);
    }
    return $files;
}

/** Get the ID by name
 * looks up the ID for a given name in multiple runs:<OL><LI>exact match</LI><LI>like ('%given name%')</LI><LI>similar ('%given%name%')</LI><LI>none</LI></OL>
 * the SQL Query base needs to ensure (via alias if necessary) the ID column is named "id"
 * @function get_idbyname
 * @param string query SQL Query base up to "WHERE"
 * @param string nf name field (e.g. "name", "title"...)
 * @param string name name to search for
 */
function get_idbyname($query,$nf,$name) {
    GLOBAL $db;
    $db->query($query . " $nf='$name'");
    if ( $db->next_record() ) return array('match'=>'exact','name'=>$name,'id'=>$db->f('id'));
    $name = "%$name%";
    $db->query($query . " $nf like '$name'");
    if ( $db->next_record() ) return array('match'=>'like','name'=>$name,'id'=>$db->f('id'));
    $name = strtolower( preg_replace('!\s+!','%',$name) );
    $db->query($query . " lower($nf) like '$name'");
    if ( $db->next_record() ) return array('match'=>'similar','name'=>$name,'id'=>$db->f('id'));
    return array('match'=>'none','name'=>$name,'id'=>0);
}

/** Get ISBNSearch URLs
 * @function get_isbnurls
 * @param string isbn ISBN to search for
 * @return array
 */
function get_isbnurls($isbn) {
   $csv = new csv(";",'"',TRUE,FALSE);
   $csv->import('./lib/isbnsearch.csv');
   $list = array();
   $isbn = preg_replace('![^0-9X]!','',$isbn);
   foreach ($csv->data as $data) {
     if ( in_array($data['name'],$GLOBALS['isbnservices']) ) {
       $url = str_replace('{isbn}',$isbn,$data['url']);
       if (preg_match('!^http://www\.amazon\.!i',$data['url'])) {
         $url = str_replace('{amazonID}',$GLOBALS['amazonID'],$url);
       }
       $list[] = array('name'=>$data['name'],'url'=>$url);
     }
   }
   return $list;
}

/** Get BookSearch URLs
 * @function get_booksearchurls
 * @param string auth author to search for
 * @param string titl title to search for
 * @return array
 */
function get_booksearchurls($auth,$titl) {
   $csv = new csv(";",'"',TRUE,FALSE);
   $csv->import('./lib/booksearch.csv');
   $list = array();
   $full = urlencode("${auth} ${titl}");
   $auth = urlencode($auth);
   $titl = urlencode($titl);
   foreach ($csv->data as $data) {
     if ( in_array($data['name'],$GLOBALS['booksearchservices']) ) {
       $url = str_replace('{author}',$auth,$data['url']);
       $url = str_replace('{title}',$titl,$url);
       $url = str_replace('{fulltext}',$full,$url);
       if (preg_match('!^http://www\.amazon\.!i',$data['url'])) {
         $url = str_replace('{amazonID}',$GLOBALS['amazonID'],$url);
       }
       $list[] = array('name'=>$data['name'],'url'=>$url);
     }
   }
   return $list;
}

/** Setup book formats (supported formats with mimetypes etc.)
 * @function get_formats
 * @return array formats
 */
function get_formats() {
   $csv = new csv(";",'"',TRUE,FALSE);
   $csv->import('./lib/formats.csv');
   $list = array();
   foreach ($csv->data as $data) {
     $list[$data['name']] = array('mimetype'=>$data['mimetype'],'ftype_human'=>$data['ftype_human'],'ftitle'=>$data['ftitle']);
   }
   return $list;
}

/** Do the pagination for lists
 * @function paginate
 * @param string url URL without the pagination element (offset)
 * @param integer offset offset for the current page
 * @param integer all total hits
 */
function paginate($url,$offset,$all) {
  $tpl = $GLOBALS['t'];
  $perpage = $GLOBALS['perpage'];
  $curpage = floor(($offset + $perpage) / $perpage); // floor in case of "manual override per URL"
  $pagecount = floor($all/$perpage);
  $tpl->set_var('offset',$offset);
  $tpl->set_var('start',$offset +1);
  $tpl->set_var('total',$all);
  $tpl->set_var('per_page',$perpage);
  if ($offset==0) { // first page
      $tpl->set_var('icon1','3left_grey.png');
      $tpl->set_var('iconx','2left_grey.png'); // HTML only
      $tpl->set_var('icon2','1left_grey.png');
      $tpl->set_var('link1_open','');
      $tpl->set_var('linkx_open',''); // HTML only
      $tpl->set_var('link2_open','');
      $tpl->set_var('link_close','');
      $tpl->set_var('poffset','0'); // OPDS only
      $tpl->parse('prev','prevblock');
  } else { // somewhere after
      $tpl->set_var('icon1','3left.png');
      $tpl->set_var('iconx','2left.png');
      $tpl->set_var('icon2','1left.png');
      $poff = max(0,$offset - $perpage);
      $tpl->set_var('poffset',$poff); // OPDS only
      $loffset = ($curpage < 6) ? 0 : ($curpage -6) * $perpage; // offset is at lower bound of perpage
      $skiptitle = ($loffset == 0) ? '' : '-5';
      $tpl->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].$url.'&amp;offset=0" TITLE="'.trans('first_page').'">');
      $tpl->set_var('linkx_open','<A HREF="'.$GLOBALS['relurl'].$url.'&amp;offset='.$loffset.'" TITLE="'.$skiptitle.'">');
      $tpl->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].$url.'&amp;offset='.$poff.'" TITLE="'.trans('prev_page').'">');
      $tpl->set_var('link_close','</A>');
      $tpl->parse('prev','prevblock');
  }
  if ($all <= $offset + $perpage) { // last page
      $tpl->set_var('icon1','1right_grey.png');
      $tpl->set_var('iconx','2right_grey.png');
      $tpl->set_var('icon2','3right_grey.png');
      $tpl->set_var('link1_open','');
      $tpl->set_var('linkx_open','');
      $tpl->set_var('link2_open','');
      $tpl->set_var('link_close','');
      $noff = $loff = floor($all/$perpage)*$perpage;
      $tpl->set_var('noffset',$noff); // OPDS only
      $tpl->set_var('loffset',$loff); // OPDS only
      $tpl->parse('next','nextblock');
  } else { // somewhere before
      $tpl->set_var('icon1','1right.png');
      $tpl->set_var('iconx','2right.png');
      $tpl->set_var('icon2','3right.png');
      $noff = $offset + $perpage; $loff = floor($all/$perpage)*$perpage;
      $tpl->set_var('noffset',$noff); // OPDS only
      $tpl->set_var('loffset',$loff); // OPDS only
      $roffset = ($pagecount - $curpage < 6) ? $pagecount * $perpage : ($curpage + 4) * $perpage; // offset is at lower bound of perpage
      $skiptitle = ($pagecount - $curpage < 6) ? '' : '+5';
      $tpl->set_var('link1_open','<A HREF="'.$GLOBALS['relurl'].$url.'&amp;offset='.$noff.'" TITLE="'.trans('next_page').'">');
      $tpl->set_var('linkx_open','<A HREF="'.$GLOBALS['relurl'].$url.'&amp;offset='.$roffset.'" TITLE="'.$skiptitle.'">');
      $tpl->set_var('link2_open','<A HREF="'.$GLOBALS['relurl'].$url.'&amp;offset='.$loff.'" TITLE="'.trans('last_page').'">');
      $tpl->set_var('link_close','</A>');
      $tpl->parse('next','nextblock');
  }
}

/** Parse titles
 *  As the same output template and structure is used by prefix=titles
 *  + prefix=searchresults, common stuff goes here
 * @function parse_titles
 * @param object tpl Template instance
 * @param object db  db instance
 * @param integer offset
 * @param integer all
 * @param string url URL for pagination
 * @param optional string prefix Usually 'titles' - but alternatively 'searchresults'
 * @param optional string searchvals additional url parameters for pagination
 */
function parse_titles(&$tpl,&$db,$offset,$all,$url,$prefix='titles',$searchvals='') {
    $tpl->set_file(array("template"=>"titles.tpl"));
    $tpl->set_block('template','itemblock','item');
    $tpl->set_block('template','prevblock','prev');
    $tpl->set_block('template','nextblock','next');
    set_basics($tpl);
    $tpl->set_var('prefix',$prefix);
    switch($prefix) {
        case 'searchresults':
            $tpl->set_var('title_list',trans('searchresults'));
            break;
        default:
            $tpl->set_var('title_list',trans('titles'));
            break;
    }
    $tpl->set_var('searchvals',$searchvals);
    $tpl->set_var('offset',$offset);
    $tpl->set_var('per_page',$GLOBALS['perpage']);
    $tpl->set_var('total',$all); // OPDS only
    $tpl->set_var('num_allbooks',$GLOBALS['allbookcount']);
    if ($GLOBALS['allbookcount']==1) $tpl->set_var('allbooks',trans('book'));
    else $tpl->set_var('allbooks',trans('books'));
    // pagination:
    $tpl->set_var('sortorder',$GLOBALS['sortorder']);
    paginate("$url&amp;pageformat=".$GLOBALS['pageformat'],$offset,$all);
    // records:
    $more = FALSE;
    while ( $db->next_record() ) {
        $tpl->set_var('bid',$db->f('id'));
        $tpl->set_var('title',$db->f('title') .' '.trans('by').' '. $db->f('name'));
        $tpl->set_var('isbn',$db->f('isbn'));
        //$tpl->set_var('pubdate',str_replace(' ','T',$db->f('timestamp')).$GLOBALS['timezone']); // pubdate is per page (current template)
        //$tpl->set_var('pubdate_human', $db->f('timestamp'));
        $tpl->parse('item','itemblock',$more);
        $more = TRUE;
    }
    $tpl->set_var('pubdate',str_replace(' ','T',$GLOBALS['pubdate']).$GLOBALS['timezone']);
    $tpl->pparse("out","template");
}

// We need the database
require_once('./lib/db_sqlite3.php');
$db = new DB_Sql();
$db->Database = $dbfile;

$db->query('SELECT COUNT(id) AS num FROM books');
$db->next_record();
$allbookcount = $db->f('num');

#========================================================[ Process request ]===
$prefix = req_word('prefix');
if ( empty($prefix) && empty($_REQUEST['action']) ) { // Startpage
    $t->set_file(array("template"=>"index.tpl"));
    set_basics($t);
    $t->set_var('author_list',trans('authors'));
    $t->set_var('title_list',trans('titles'));
    $t->set_var('tags_list',trans('tags'));
    $t->set_var('series_list',trans('series'));
    $t->set_var('search_form',trans('search'));
    $t->set_var('num_allbooks',$allbookcount);
    if ($allbookcount==1) $t->set_var('allbooks',trans('book'));
    else $t->set_var('allbooks',trans('books'));
    $t->pparse("out","template");
    exit;
}
$offset = req_int('offset');

switch($prefix) {
    //---------------------------------------------[ search form requested ]---
    case 'search':
        $tpl = new Template('tpl/html'); // NO OPDS TEMPLATE FOR THIS!
        $t->set_file(array("template"=>"search.tpl"));
        $t->set_block('template','tagselblock','tagsel');
        set_basics($t);
        $t->set_var('form_action','?prefix=searchresults&amp;lang='.$GLOBALS['use_lang'].'&amp;pageformat='.$pageformat);
        $t->set_var('bsearch',trans('book_search_title'));
        $t->set_var('author_title',trans('author'));
        $t->set_var('book_title',trans('book'));
        $t->set_var('series_title',trans('serie'));
        $t->set_var('tags_title',trans('tags'));
        $t->set_var('desc_title',trans('comment'));
        $t->set_var('submit_title',trans('search_do'));
        $t->set_var('num_allbooks',$allbookcount);
        if ($allbookcount==1) $t->set_var('allbooks',trans('book'));
        else $t->set_var('allbooks',trans('books'));
        $db->query('SELECT id,name FROM tags ORDER BY name');
        $more = FALSE;
        while ( $db->next_record() ) {
            $t->set_var('optname',$db->f('name'));
            $t->set_var('optval',$db->f('id'));
            $t->parse('tagsel','tagselblock',$more);
            $more = TRUE;
        }
        $t->pparse("out","template");
        exit;
        break;
    //-------------------------------------[ OpenSearch document requested ]---
    case 'ods':
        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"
           . ' <OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">'."\n"
           . "  <ShortName>$sitetitle</ShortName>\n"
           . "  <Description>$sitetitle: ".trans('book_search_title')."</Description>\n"
           . "  <Tags>ebooks</Tags>\n"
           . "  <Contact>$email</Contact>\n"
           . '  <Url type="application/atom+xml" template="'.$baseurl.'?lang='.$use_lang.'&amp;pageformat=opds&amp;prefix=searchresults&amp;q={searchTerms}"/>'."\n"
           . '  <Url type="text/html"            template="'.$baseurl.'?lang='.$use_lang.'&amp;pageformat=html&amp;prefix=searchresults&amp;q={searchTerms}"/>'."\n"
           . '  <Image height="22" width="22" type="image/png">'.$baseurl."tpl/icons/find.png</Image>\n"
           . "  <OutputEncoding>UTF-8</OutputEncoding>\n"
           . "  <InputEncoding>UTF-8</InputEncoding>\n"
           . "  <Language>".$use_lang."</Language>\n"
           . " </OpenSearchDescription>\n";
        exit;
    //--------------------------------------[ search result list requested ]---
    case 'searchresults':
        $sall = req_alnumwild('q');
        $saut = req_alnumwild('author');
        $stit = req_alnumwild('title');
        $sser = req_alnumwild('series');
        $stxt = req_alnumwild('desc');
        $stag = req_intarr('tags');
        $logger->debug("q=$sall;author=$saut;title=$stit;series=$sser;desc=$stxt;tags=".implode(',',$stag).";offset=$offset",'SEARCH');
        $searchreq = "q=$sall&amp;author=$saut&amp;title=$stit&amp;series=$sser&amp;desc=$stxt&amp;tags=".implode(',',$stag);

        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title': $order = ' ORDER BY b.title'; $sortorder='title'; break;
            case 'name' : $order = ' ORDER BY a.name'; $sortorder='name'; break;
            case 'time' : $order = ' ORDER BY b.timestamp DESC'; $sortorder='time'; break;
            default     : $order = ''; $sortorder=''; break;
        }
        $select = 'SELECT b.id,b.title,b.isbn,a.name,b.timestamp FROM books b,books_authors_link bl,authors a';
        $where = $searchvals = '';
        if ( empty($sall) ) {
            if ( !empty($saut) ) {
              $where .= " AND lower(a.name) LIKE '%".strtolower($saut)."%'";
              $searchvals .= '&amp;author='.urlencode($saut);
            }
            if ( !empty($stit) ) {
              $where .= " AND lower(b.title) LIKE '%".strtolower($stit)."%'";
              $searchvals .= '&amp;title='.urlencode($stit);
            }
            if ( !empty($stag) ) {
              $select .= ',books_tags_link bt';
              $where .= ' AND bt.book=b.id AND bt.tag IN ('.implode(',',$stag).')';
              foreach ($stag as $tt) $searchvals .= '&amp;tags[]='.$tt;
            }
            if ( !empty($sser) ) {
              $select .= ',books_series_link bs,series s';
              $where  .= " AND bs.book=b.id AND bs.series=s.id AND lower(s.name) LIKE '%".strtolower($sser)."%'";
              $searchvals .= '&amp;series='.urlencode($sser);
            }
            if ( !empty($stxt) ) {
              $select .= ',comments c';
              $where  .= " AND c.book=b.id AND lower(c.text) LIKE '%".strtolower($stxt)."%'";
              $searchvals .= '&amp;desc='.urlencode($stxt);
            }
        } else {
            $sterm = '%'.strtolower($sall).'%';
            $select     .= ',comments c';
            $where      .= " AND c.book=b.id AND ( lower(c.text) LIKE '$sterm' OR lower(b.title) LIKE '$sterm' OR lower(a.name) LIKE '$sterm' )";
            $searchvals .= '&amp;q='.urlencode($sall);
        }
        $select .= ' WHERE b.id=bl.book and a.id=bl.author '.$where;

        $logger->debug($select.$order,'SEARCH');
        $all = $db->lim_query($select.$order, $offset, $perpage);
        parse_titles($t,$db,$offset,$all,'?prefix=searchresults&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;'.$searchreq,'searchresults',$searchvals);
        exit;
        break;
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
            case 'books': $order = ' ORDER BY num DESC'; $sortorder='books'; break;
            case 'title':
            default     : $order = ' ORDER BY name'; $sortorder='title'; break;
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
        paginate('?prefix=authors&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;pageformat='.$pageformat,$offset,$num_authors);
        $t->pparse("out","template");
        exit;
    //------------------------[ List of books for a given author requested ]---
    case 'author_id':
        $aname = req_alnum('name');
        if ( empty($aname) ) $aid = req_int('query');
        else {
            $sarr = get_idbyname('SELECT id FROM authors WHERE','name',$aname);
            $aid = (int) $sarr['id'];
        }
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
            case 'date' : $order = ' ORDER BY timestamp DESC'; $sortorder='date'; break;
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
        $t->set_var('sortorder',$sortorder);
        paginate('?prefix=author_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;query='.$aid.'&amp;pageformat='.$pageformat,$offset,$all);
        $t->pparse("out","template");
        exit;
    //------------------------------[ List of all books by title requested ]---
    case 'titles':
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'title': $order = ' ORDER BY b.title'; $sortorder='title'; break;
            case 'name' : $order = ' ORDER BY a.name'; $sortorder='name'; break;
            case 'time' : $order = ' ORDER BY b.timestamp DESC'; $sortorder='time'; break;
            default     : $order = ''; $sortorder=''; break;
        }
        $all = $db->lim_query('SELECT b.id,b.title,b.isbn,a.name,b.timestamp FROM books b,books_authors_link bl,authors a WHERE b.id=bl.book and a.id=bl.author '.$order, $offset, $perpage);
        parse_titles($t,$db,$offset,$all,'?prefix=titles&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder);
        exit;
    //----------------------------------------[ List of all tags requested ]---
    case 'tags':
        $sortorder = req_word('sort_order');
        switch($sortorder) {
            case 'books': $order = ' ORDER BY num DESC'; $sortorder='books'; break;
            case 'title':
            default     : $order = ' ORDER BY name'; $sortorder='title'; break;
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
        $t->set_var('sortorder',$sortorder);
        paginate('?prefix=tags&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;pageformat='.$pageformat,$offset,$all);
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
        $tag_name = req_alnum('name');
        if ( empty($tag_name) ) $tag_id = req_int('query');
        else {
            $sarr = get_idbyname('SELECT id FROM tags WHERE','name',$tag_name);
            $tag_id = (int) $sarr['id'];
        }
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
        $t->set_var('sortorder',$sortorder);
        paginate('?prefix=tag_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;query='.$tag_id.'&amp;pageformat='.$pageformat,$offset,$all);
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
        $t->set_var('sortorder',$sortorder);
        paginate('?prefix=series&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;pageformat='.$pageformat,$offset,$all);
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
        $series_name = req_alnum('name');
        if ( empty($series_name) ) $series_id = req_int('query');
        else {
            $sarr = get_idbyname('SELECT id,name FROM series WHERE','name',$series_name);
            $series_id = (int) $sarr['id'];
        }
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
        $t->set_var('sortorder',$sortorder);
        paginate('?prefix=series_id&amp;lang='.$GLOBALS['use_lang'].'&amp;sort_order='.$sortorder.'&amp;query='.$series_id.'&amp;pageformat='.$pageformat,$offset,$all);
        $t->pparse("out","template");
        exit;
    //----------------------------------------------[ Handle a single book ]---
    case '':
        // Display book details
        if (isset($_REQUEST['action']) && $_REQUEST['action']=='bookdetails') {
            $bname = req_alnum('name');
            if ( empty($bname) ) $bookid = req_int('book');
            else {
                $sarr = get_idbyname('SELECT id FROM books WHERE','title',$bname);
                $bookid = (int) $sarr['id'];
            }
            if ( isset($sarr) && $sarr['match'] == 'none' ) {
              $author = trans('not_available');
              goto NoSuchBook;
            }
            $db->query("SELECT id,name FROM authors WHERE id IN (SELECT author FROM books_authors_link WHERE book=$bookid)");
            $author = '';
            while ( $db->next_record() ) {
                $author .= ', ' . $db->f('name');
                $authors[] = array('id'=>$db->f('id'),'name'=>$db->f('name'));
            }
            $author = substr($author,2);
            if ( $cover_mode == 'calibre' ) $db->query("SELECT title,isbn,series_index,strftime('%Y-%m-%dT%H:%M:%S',timestamp) pubdate,'' as uri FROM books WHERE id=".$bookid);
            else $db->query("SELECT title,isbn,series_index,strftime('%Y-%m-%dT%H:%M:%S',timestamp) pubdate,uri FROM books WHERE id=".$bookid);
            if ( !$db->next_record() ) { // we don't have a book with this ID
              NoSuchBook:
              header("HTTP/1.0 404 Not Found");
              $book = array('title'=>trans('not_available'),'isbn'=>'','tags'=>'','series'=>'','series_index'=>'','uri'=>'','author'=>trans('not_available'),
                      'pubdate'=>'1970-01-01T00:00:00','pubdate_human'=>'01-01-1970 00:00','publisher'=>'','comment'=>trans('no_such_book'),'rating'=>0);
              $files = $authors = array();
              goto Parse;
            }
            $book = array(
              'title'=>$db->f('title'), 'isbn'=>$db->f('isbn'), 'tags'=>'', 'series_index'=>$db->f('series_index'),
              'uri'=>$db->f('uri'), 'author'=>$author,
              'pubdate'=>$db->f('pubdate').$timezone, 'pubdate_human'=>date("d-m-Y H:i",strtotime($db->f('pubdate')))
            );
            $db->query("SELECT id,name FROM tags WHERE id IN (SELECT tag FROM books_tags_link WHERE book=$bookid)");
            while ( $db->next_record() ) {
                $book['tags'] .= ', '.$db->f('name');
            }
            if (strlen($book['tags'])) $book['tags'] = substr($book['tags'],2);
            $db->query("SELECT id,name FROM series WHERE id IN (SELECT series FROM books_series_link WHERE book=$bookid)");
            $db->next_record();
            $book['series'] = $book['series_name'] = $db->f('name');
            $book['series_id'] = $db->f('id');
            if (!empty($book['series'])) $book['series'] .= ' (#'.$book['series_index'].')';
            $db->query("SELECT name FROM publishers WHERE id IN (SELECT publisher FROM books_publishers_link WHERE book=$bookid)");
            $db->next_record();
            $book['publisher'] = $db->f('name');
            $db->query("SELECT text FROM comments WHERE book=$bookid");
            $db->next_record();
            $book['comment'] = htmlentities($db->f('text'));
            $db->query("SELECT r.rating FROM ratings r, books_ratings_link br WHERE br.book=$bookid AND r.id=br.rating");
            if ( $db->next_record() ) {
              if ( $cover_mode == 'calibre' ) $book['rating'] = round( $db->f('rating') / 2); // Calibre has 0..10, we have only 0..5
              else $book['rating'] = $db->f('rating');
            } else $book['rating'] = 0;
            $files = get_filenames($db,$bookid);
            Parse:
            $t->set_file(array("template"=>"book.tpl"));
            $t->set_block('template','authorblock','author');
            $t->set_block('template','serialblock','serial');
            $t->set_block('template','flattrblock','flattr');
            $t->set_block('template','datablock','data');
            $t->set_block('template','itemblock','item');
            $t->set_block('template','coverblock','cover');
            $t->set_block('template','fakecoverblock','fakecover');
            $t->set_block('template','adblock','ads');
            set_basics($t);
            $t->set_var('back_to_authors',trans('back_to_authors'));
            $t->set_var('data_name',trans('title'));
            $t->set_var('data_data',$book['title']);
            $t->parse('data','datablock');
            // Do the FlattR
            if ( empty($GLOBALS['flattrID']) || empty($authors) ) {
              $t->set_var('flattr','');
            } else {
              $t->set_var('flattrID',$GLOBALS['flattrID']);
              $t->set_var('flattred_url',urlencode($GLOBALS['baseurl'].'?lang='.$GLOBALS['use_lang'].'pageformat=html&action=bookdetails&book='.$bookid));
              $t->parse('flattr','flattrblock');
            }
            $more = FALSE;
            foreach ($authors as $aut) {
                $t->set_var('aid',$aut['id']);
                $t->set_var('authors_page',trans('all_books_by_whom',$aut['name']));
                $t->parse('author','authorblock',$more);
                $more = TRUE;
            }
            $more = FALSE;
            if ( isset($book['series_id']) ) {
                $t->set_var('id',$book['series_id']);
                $t->set_var('series_page',trans('all_books_in_serie',$book['series_name']));
                $t->parse('serial','serialblock',$more);
                $more = TRUE;
            }
            foreach (array('author','isbn','tags','series','publisher','uri','rating') as $field) {
                if ($field=='tags' && $pageformat=='html' && !empty($booksearchservices) && !empty($authors)) { // book-search; this should follow the ISBNs
                  $iurls = get_booksearchurls(str_replace(',',' ',$author),$book['title']);
                  $text  = "<SPAN ID='booksearch'>";
                  foreach ($iurls as $iurl) $text .= " <A HREF='".$iurl['url']."'>".$iurl['name']."</A>";
                  $text .= "</SPAN>";
                  $t->set_var('data_name',trans('title_websearch'));
                  $t->set_var('data_data',$text);
                  $t->parse('data','datablock',TRUE);
                }
                if ( empty($book[$field]) ) continue;
                if ($field=='series') $t->set_var('data_name',trans('serie'));
                else $t->set_var('data_name',trans($field));
                switch ($field) {
                  case 'uri' :
                    $t->set_var('data_data',"<A HREF='".$book[$field]."'>".$book[$field]."</A>");
                    $t->set_var('dataclass'," STYLE='line-height:1.6em;'");
                    break;
                  case 'isbn':
                    $iurls = get_isbnurls($book[$field]);
                    $text  = $book[$field];
                    if ($pageformat=='html') {
                      $text .= "<SPAN ID='isbnsearch'>";
                      foreach ($iurls as $iurl) $text .= " <A HREF='".$iurl['url']."'>".$iurl['name']."</A>";
                      $text .= "</SPAN>";
                    }
                    $t->set_var('data_data',$text);
                    $t->set_var('dataclass'," STYLE='line-height:1.6em;'");
                    break;
                  default    :
                    $t->set_var('data_data',$book[$field]);
                    $t->set_var('dataclass','');
                    break;
                }
                $t->parse('data','datablock',TRUE);
            }
            if ( !empty($book['rating']) ) {
                $t->set_var('data_name',trans('rating'));
                $t->set_var('data_data','<IMG SRC="'.$relurl.'tpl/icons/rating_'.$book['rating'].'.gif" ALT="Rating '.$book['rating'].'"/>');
                $t->parse('data','datablock',TRUE);
            }
            $t->set_var("field_download",trans('download'));
            $t->set_var('id',$bookid);
            $t->set_var('title_by_author',trans('title_by_author',$book['title'],$author));
            $t->set_var('booktitle',$book['title']); // used by OPDS only + fakecover
            $t->set_var('authorname',$author); // used by OPDS only + fakecover
            $t->set_var('field_comment',trans('comment'));
            if ( empty($book['comment']) ) {
                if ( $pageformat == 'opds' ) $t->set_var('comment',trans('not_available'));
                else  $t->set_var('comment',trans('comment').' '.trans('not_available'));
            } else {
                $comm = html_entity_decode($book['comment']);
                if ( $pageformat=='opds' ) {
                  $comm = preg_replace('!(</?\w+)(.*?>)!e', 'strtolower("\\1") . "\\2"', $comm); // OPDS/XML wants tags lower-case!
                  $comm = preg_replace("/ (CLASS|ID|SRC|HREF|ALT)=/e", "' ' . strtolower('\\1') . '='", $comm);     // Same for attributes
                  $comm = stripslashes($comm);
                }
                $t->set_var('comment',$comm);
            }
            $t->set_var('pubdate',$book['pubdate']);
            $t->set_var('pubdate_human',$book['pubdate_human']);
            $more = FALSE;
            $formats = get_formats();
            foreach ($files as $file) {
                $file['format'] = strtolower($file['format']); // Calibre uses UPPERcase
                if ( empty($formats[$file['format']]) ) {
                  $logger->error('Unsupported format "'.$file['format'].'" for book "'.$file['name'].'"','DETAILS');
                  continue;
                }
                $t->set_var('ftype',$formats[$file['format']]['mimetype']);
                $t->set_var('ftype_human',$formats[$file['format']]['ftype_human']);
                $t->set_var('ftitle',$formats[$file['format']]['ftitle']);
                $covername = $file['path'].DIRECTORY_SEPARATOR.$file['name']; // file w/o ext
                $t->set_var('flength',$file['size']);
                $t->set_var('flength_human',number_format(round($file['size']/1024)).'kB');
                $t->set_var('format',$file['format']);
                $t->parse('item','itemblock',$more);
                $more = TRUE;
            }
            // book cover
            if ($use_lang=='cal') $covername = $cover_base.DIRECTORY_SEPARATOR.$use_lang.DIRECTORY_SEPARATOR.$bookid;
            $coverimg = $filefuncs->getCover($covername);
            if ( !empty($coverimg) && file_exists($coverimg) && is_readable($coverimg) ) {
                $cover_type = pathinfo($coverimg)['extension']; if ( $cover_type == 'jpg' ) $cover_type = 'jpeg';
                $t->set_var('cover_type',$cover_type); // MimeType (opds only)
                $t->set_var('cover_src',str_replace(' ','%20',$coverimg));
                $t->set_var('cover_width',$cover_width);
                $t->parse('cover','coverblock');
            } elseif ($cover_fake_fallback) {
                $t->parse('fakecover','fakecoverblock');
            }
            // Ads (if wanted)
            if ( $ads_bookdetails ) {
                $t->set_var('amazonID',$amazonID);
                $t->set_var('amazon_bordercolor',$ads_bordercolor);
                $t->set_var('amazon_logocolor',$ads_logocolor);
                $t->set_var('booktitle_urlenc',urlencode($book['title'])); // used for Amazon ads
                $t->set_var('authorname_urlenc',urlencode($author)); // used for Amazon ads
                $t->set_var('booktags_urlenc',urlencode(str_replace(', ',';',$book['tags']))); // used for Amazon ads
                $t->parse('ads','adblock');
            } else {
                $t->set_var('ads','');
            }
            // end ads
            $t->pparse("out","template");
            exit;
        // Send the requested book for download
        } elseif (isset($_REQUEST['action']) && $_REQUEST['action']=='getbook') { // bookid=req[book], req[format] = epub/mobi
            $bname = req_alnum('name');
            if ( empty($bname) ) $bookid = req_int('book');
            else {
                $sarr = get_idbyname('SELECT id FROM books WHERE','title',$bname);
                $bookid = (int) $sarr['id'];
            }
            $files = get_filenames($db,$bookid,req_word('format'));
            $book  = $files[0]['path'].'/'.$files[0]['name'].'.'.strtolower($files[0]['format']);
            if ($fd = fopen ($book,"rb")) {
                if ( empty($dllogfile) ) { // log DL to default log if no special log is set up
                    $logger->info($book,'DOWNLOAD');
                } else {
                    $dllogger->info($book,'DOWNLOAD');
                }
                $fformats = get_formats();
                header("Content-type: ".$fformats[strtolower($files[0]['format'])]['mimetype']); // Calibre uses UPPERcase
                header("Content-Disposition: attachment; filename=\"".$files[0]['name'].'.'.strtolower($files[0]['format']."\""));
                header("Content-length: ".filesize($book));
                fpassthru($fd);
                fclose($fd);
                exit;
            }
            exit;
        }
}
?>