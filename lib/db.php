<?
 #############################################################################
 # miniCalOPe                               (c) 2010-2011 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

require_once(dirname(__FILE__).'/common.php'); // for debug output

class db extends DB_Sql {

  function __construct($dbfile) {
    $this->Database = $dbfile;
  }

  //==============================================================[ Helper ]===
  /** Process a query making sure a faulty statement does not halt the engine
   * @method private query_nohalt
   * @param string sql SQL-Statement to process
   * @return mixed QUERY_ID or FALSE
   */
  private function query_nohalt($sql) {
    $halt = $this->Halt_On_Error;
    $this->Halt_On_Error = 'no';
    $rc = $this->query($sql);
    $this->Halt_On_Error = $halt;
    return $rc;
  }

  //==========================================================[ DB Feeding ]===
  /** Truncate all tables. This is done prior to a new import.
   * @method truncAll
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function truncAll($who='SCAN') {
    $GLOBALS['logger']->info('  + Truncating Tables',$who);
    if ( strtolower($GLOBALS['scan_dbmode'])=='merge' ) {
      $tables = array('comments','books_authors_link','books_publishers_link',
                      'books_ratings_link','books_series_link','books_tags_link');
    } else {
      $tables = array('comments','books','authors','tags','series','publishers',
                      'books_authors_link','books_publishers_link','books_ratings_link',
                      'books_series_link','books_tags_link','data');
    }
    foreach($tables as $table) $this->query("DELETE FROM $table");
    $this->query('VACUUM');
  }

  /** Feed the genres into the database
   * @method make_genres
   * @param array genres array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_genres($genres,$who='SCAN') {
    $genres = array_unique($genres);
    $GLOBALS['logger']->info('  + Inserting Tags ('.count($genres).')',$who);
    $i=0;
    if ( strtolower($GLOBALS['scan_dbmode'])=='merge' ) {
      $this->query("SELECT name FROM tags");
      $dbcats = array();
      while ( $this->next_record() ) $dbcats[] = $this->f('name');
      $delcats = array_diff($dbcats,$genres); // in DB, but not in files
      $list = implode("','",$delcats);
      if ( !empty($list) ) $this->query("DELETE FROM tags WHERE name IN ('$list')");
      $delcats = array_diff($genres,$dbcats); // in files, but not in DB
      $genres = $delcats; // those are left for insert
      $this->query("SELECT MAX(id)+1 AS nextid FROM tags");
      $this->next_record();
      $i = $this->f('nextid');
    }
    $this->query('BEGIN TRANSACTION');
    foreach($genres AS $genre) {
      $this->query("INSERT INTO tags(id,name) VALUES ($i,'$genre')");
      ++$i;
    }
    $this->query('COMMIT TRANSACTION');
  }

  /** Feed the publisher into the database
   * @method make_publisher
   * @param array publisher array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_publisher($publisher,$who='SCAN') {
    $publisher = array_unique($publisher);
    $GLOBALS['logger']->info('  + Inserting Publisher ('.count($publisher).')',$who);
    // preparation: Escape strings
    $pub = array();
    foreach($publisher AS $genre) $pub[] = $this->escape($genre);
    $publisher = array_unique($pub); unset($pub);
    $i=0;
    $this->query('BEGIN TRANSACTION');
    if ( strtolower($GLOBALS['scan_dbmode'])=='merge' ) {
      $this->query("SELECT name FROM publishers");
      $dbcats = array();
      while ( $this->next_record() ) $dbcats[] = $this->escape($this->f('name'));
      $delcats = array_diff($dbcats,$publisher); // in DB, but not in files
      $list = implode("','",$delcats);
      if ( !empty($list) ) $this->query("DELETE FROM publishers WHERE name IN ('$list')");
      $delcats = array_diff($publisher,$dbcats); // in files, but not in DB
      $publisher = $delcats; // those are left for insert
      $this->query("SELECT MAX(id)+1 AS nextid FROM publishers");
      $this->next_record();
      $i = $this->f('nextid');
    }
    foreach($publisher AS $genre) {
      if ( $this->query_nohalt("SELECT id FROM publishers WHERE name='$genre'") ) {
        if ( !$this->next_record() ) {
          $this->query("INSERT INTO publishers(id,name) VALUES ($i,'$genre')");
          ++$i;
        }
      } else {
        $GLOBALS['logger']->error("SQL Error for publisher [$genre] (".$this->Error.")",$who);
      }
    }
    if ( !$this->query_nohalt('COMMIT TRANSACTION') ) $this->query_nohalt('COMMIT TRANSACTION'); // hick-ups
  }

  /** Feed the authors into the database
   * @method make_authors
   * @param array authors array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_authors($authors,$who='SCAN') {
    $authors = array_unique($authors);
    $GLOBALS['logger']->info('  + Inserting Authors ('.count($authors).')',$who);
    $i=0;
    $this->query('BEGIN TRANSACTION');
    if ( strtolower($GLOBALS['scan_dbmode'])=='merge' ) {
      $this->query("SELECT name FROM authors");
      $dbcats = array();
      while ( $this->next_record() ) $dbcats[] = $this->f('name');
      $delcats = array_diff($dbcats,$authors); // in DB, but not in files
      $list = implode(',',$delcats);
      if ( !empty($list) ) $this->query("DELETE FROM authors WHERE name IN ('$list')");
      $delcats = array_diff($authors,$dbcats); // in files, but not in DB
      $authors = $delcats; // those are left for insert
      $this->query("SELECT MAX(id)+1 AS nextid FROM authors");
      $this->next_record();
      $i = $this->f('nextid');
    }
    foreach($authors AS $author) {
      $this->query("INSERT INTO authors(id,name) VALUES ($i,'$author')");
      ++$i;
    }
    if ( !$this->query_nohalt('COMMIT TRANSACTION') ) $this->query_nohalt('COMMIT TRANSACTION'); // hick-ups
  }

  /** Feed the series into the database
   * @method make_series
   * @param array series array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_series($series,$who='SCAN') {
    $series = array_unique($series);
    $GLOBALS['logger']->info('  + Inserting Series ('.count($series).')',$who);
    $i=0;
    // preparation: escape strings
    $ser = array();
    foreach($series as $serie) $ser[] = $this->escape($serie);
    $series = array_unique($ser);
    $this->query('BEGIN TRANSACTION');
    if ( strtolower($GLOBALS['scan_dbmode'])=='merge' ) {
      $this->query("SELECT name FROM series");
      $dbcats = array();
      while ( $this->next_record() ) $dbcats[] = $this->f('name');
      $delcats = array_diff($dbcats,$series); // in DB, but not in files
      $list = implode(',',$delcats);
      if ( !empty($list) ) $this->query("DELETE FROM series WHERE name IN ('$list')");
      $delcats = array_diff($series,$dbcats); // in files, but not in DB
      $series = $delcats; // those are left for insert
      $this->query("SELECT MAX(id)+1 AS nextid FROM series");
      $this->next_record();
      $i = $this->f('nextid');
    }
    foreach($series as $serie) {
      $this->query("INSERT INTO series(id,name) VALUES ($i,'$serie')");
      ++$i;
    }
    if ( !$this->query_nohalt('COMMIT TRANSACTION') ) $this->query_nohalt('COMMIT TRANSACTION'); // hick-ups
  }

  /** Feed the books into the database
   * @method make_books
   * @param array books array[string bookname] w/ props str lang, str genre, array tag, array author,
   *        str title, str series, str series_index, str rating, str publisher, str isbn, array files[str type]=str filename
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_books($books,$who='SCAN') {
    $mode = strtolower($GLOBALS['scan_dbmode']);
    if ( $mode=='merge' ) {
      $deleted = $this->removed_books($books);
      $this->query("SELECT MAX(id)+1 AS nextid FROM books");
      $this->next_record();
      $b_id  = $this->f('nextid');
      $this->query("SELECT MAX(id)+1 AS nextid FROM data");
      $this->next_record();
      $d_id  = $this->f('nextid');
      $GLOBALS['logger']->info('  + Merging Books ('.count($books).')',$who);
    } else {
      $b_id=0;
      $d_id=0;
      $GLOBALS['logger']->info('  + Inserting Books ('.count($books).')',$who);
    }
    $ba_id=0; $bt_id=0; $bs_id=0; $bp_id=0; $br_id=0; $c_id=0;
    $count = array('added'=>0,'moved'=>0,'merged'=>0);

    $this->query('BEGIN TRANSACTION');
    foreach($books as $name=>$dummy) {
      $a_id=array(); $t_id=array();
      if ( !isset($books[$name]['files']) || !is_array($books[$name]['files']) ) {
        $GLOBALS['logger']->error('! The book "'.$name.'" (author: "'.$books[$name]['author'][0].'", genre "'.$books[$name]['genre'].'") seems to have no files!',$who);
        continue;
      }
      foreach($books[$name]['files'] as $file) { // cannot address numerical - why?
        $pos = strrpos($file,DIRECTORY_SEPARATOR);
        $path = substr($file,strlen($GLOBALS['bookroot']),$pos-strlen($GLOBALS['bookroot']));
        break;
      }
      $bf = ''; $bv = ''; $updf = '';
      foreach(array('isbn','uri') as $fn) if (isset($books[$name][$fn])) {
        $bf .= ",$fn"; $bv .= ",'".$books[$name][$fn]."'";
        $updf .= ",$fn='".$books[$name][$fn]."'";
      }
      if (isset($books[$name]['series_index'])) {
        $bf .= ",series_index"; $bv .= ",".$books[$name]['series_index'];
        $updf .= ",series_index='".$books[$name]['series_index']."'";
      }
      if ( empty($books[$name]['title']) ) $btitle = $this->escape($name);
      else $btitle = $this->escape($books[$name]['title']);
      if ( $mode=='merge' ) { // match book on path and filebasename
        $this->query("SELECT b.id AS book_id FROM books b, data d WHERE b.id=d.book AND d.name='".$books[$name]['fbasename']."' AND b.path='$path'");
        if ( $this->next_record() ) {
          $bookid = $this->f('book_id');
          $ebookid = $bookid;
          $query  = "UPDATE books SET title='$btitle',timestamp='".date('Y-m-d H:i:s',$books[$name]['lastmod'])."'$updf WHERE id=$bookid";
          ++$count['merged'];
        } else {
          $found = FALSE;
          foreach ($books[$name]['files'] as $val) { $tbasename = $val; break; }
          foreach ($deleted as $del) { // check for details in $deleted for retainable IDs
            if ( $btitle==$del['title'] || $tbasename==$del['basename'] ) { // title or filename match
              $bookid = $del['id'];
              $ebookid = $del['id'];
              $query = "INSERT INTO books(id,title,path,timestamp".$bf.") VALUES ($ebookid,'$btitle','$path','".date('Y-m-d H:i:s',$books[$name]['lastmod'])."'".$bv.")";
              ++$count['moved'];
              $found = TRUE;
              break;
            }
          }
          if ( !$found ) {
            $bookid = -1;
            $ebookid = $b_id;
            $query = "INSERT INTO books(id,title,path,timestamp".$bf.") VALUES ($b_id,'$btitle','$path','".date('Y-m-d H:i:s',$books[$name]['lastmod'])."'".$bv.")";
            ++$count['added'];
          }
        }
      } else { // fresh run (no merge)
        $bookid = -1;
        $ebookid = $b_id;
        $query = "INSERT INTO books(id,title,path,timestamp".$bf.") VALUES ($b_id,'$btitle','$path','".date('Y-m-d H:i:s',$books[$name]['lastmod'])."'".$bv.")";
        ++$count['added'];
      }
      if ( !$this->query_nohalt($query) ) {
        $GLOBALS['logger']->error('! Error inserting book "'.$name.'" (author: "'.$books[$name]['author'][0].'", genre "'.$books[$name]['genre'].'")',$who);
        $GLOBALS['logger']->error('! ('.$this->Error.')');
        continue;
      }

      // ebook files
      $tfiles = array();
      if ( $mode=='merge' ) {
        $this->query("SELECT id,format FROM data WHERE book=$ebookid");
        while ( $this->next_record() ) $tfiles[$this->f('format')] = $this->f('id');
        $this->query("DELETE FROM data WHERE book=$ebookid"); // rebuild them in either case
      }
      foreach($books[$name]['files'] as $var=>$val) {
        if ( isset($tfiles[$var]) ) { // re-use ID
          $this->query("INSERT INTO data(id,book,format,uncompressed_size,name) VALUES (".$tfiles[$var].",$ebookid,'$var',".filesize($val).",'".$books[$name]['fbasename']."')");
        } else {
          $this->query("INSERT INTO data(id,book,format,uncompressed_size,name) VALUES ($d_id,$ebookid,'$var',".filesize($val).",'".$books[$name]['fbasename']."')");
          ++$d_id;
        }
      }

      // relation to authors
      $anames = '';
      $books[$name]['author'] = array_unique($books[$name]['author']);
      foreach($books[$name]['author'] as $aut) $anames .= ",'".$aut."'";
      $this->query("SELECT id FROM authors WHERE name IN (".substr($anames,1).")");
      while ($this->next_record()) $a_id[] = $this->f('id');
      if ( is_array($a_id) ) {
          foreach($a_id as $aid) {
              $this->query("INSERT INTO books_authors_link (id,book,author) VALUES ($ba_id,$ebookid,$aid)");
              ++$ba_id;
          }
      } else {
          $GLOBALS['logger']->error('! The book "'.$name.'" (author: "'.$books[$name]['author'][0].'", genre "'.$books[$name]['genre'].'") seems to have no author!',$who);
      }

      // relation to tags/genres
      $books[$name]['tag'][] = $books[$name]['genre'];
      $books[$name]['tag'] = array_unique($books[$name]['tag']);
      $tnames = '';
      foreach($books[$name]['tag'] as $aut) $tnames .= ",'".$aut."'";
      $this->query("SELECT id FROM tags WHERE name IN (".substr($tnames,1).")");
      while ($this->next_record()) $t_id[] = $this->f('id');
      if ( is_array($t_id) ) {
          foreach($t_id as $tid)  {
              $this->query("INSERT INTO books_tags_link(id,book,tag) VALUES($bt_id,$ebookid,$tid)");
              ++$bt_id;
          }
      } else {
          $GLOBALS['logger']->error('! No tag IDs found in DB for the book "'.$name.'" (author: "'.$books[$name]['author'][0].'", genre "'.$books[$name]['genre'].'")!',$who);
      }

      // relation to series
      if ( isset($books[$name]['series']) ) {
        $this->query_nohalt("SELECT id FROM series WHERE name='".$this->escape($books[$name]['series'])."'");
        if ( $this->next_record() ) {
          $s_id = $this->f('id');
          $this->query("INSERT INTO books_series_link(id,book,series) VALUES ($bs_id,$ebookid,$s_id)");
          ++$bs_id;
        }
      }

      // relation to publisher
      if ( isset($books[$name]['publisher']) ) {
        $publisher = $this->escape($books[$name]['publisher']);
        if ( $this->query_nohalt("SELECT id FROM publishers WHERE name='$publisher'") ) {
          if ( $this->next_record() ) {
            $p_id = $this->f('id');
            $this->query("INSERT INTO books_publishers_link(id,book,publisher) VALUES ($bp_id,$ebookid,$p_id)");
            ++$bp_id;
          }
        } else {
          $GLOBALS['logger']->error("! SQL Error for publisher [".$books[$name]['publisher']."] (".$this->Error.")",'SCAN');
          $GLOBALS['logger']->error("! (book: '$name', author: '".$books[$name]['author'][0].", genre: '".$books[$name]['genre']."')",$who);
        }
      }

      // relation to ratings
      if ( !empty($books[$name]['rating']) ) {
        $this->query("INSERT INTO books_ratings_link(id,book,rating) VALUES ($br_id,$ebookid,".$books[$name]['rating'].")");
        ++$br_id;
      }

      // Detailled description
      if ( !empty($books[$name]['desc']) ) {
        $this->query("INSERT INTO comments(id,book,text) VALUES($c_id,$ebookid,'".$this->escape($books[$name]['desc'])."')");
        ++$c_id;
      }
      if ( $mode!='merge' || $bookid < 0 ) ++$b_id;
    }
    $this->query('COMMIT TRANSACTION');
    $GLOBALS['logger']->info('    added: '.$count['added'].', moved: '.$count['moved'].', merged: '.$count['merged'],$who);
    $GLOBALS['logger']->info("    eBook files: $d_id",$who);
  }

  /** check for books deleted on the file system and remove them from DB as well
   *  This will check the passed array ($books) and remove all books not contained in that array from the database.
   *  Information on the removed books then is returned for re-insertion with the same book id in case the book was simply moved.
   * @method removed_books
   * @param array books array[string bookname] w/ props str lang, str genre, array tag, array author,
   *        str title, str series, str series_index, str rating, str publisher, str isbn, array files[str type]=str filename
   * @param opt string who who is calling this? defaults to 'SCAN', which is the usual candidate
   * @return array deleted info on "deleted" books in case they have only been moved (array[id,fullname,title,basename])
   */
  public function removed_books(&$books,$who='SCAN') {
    $GLOBALS['logger']->info('  + Checking for (re)moved books',$who);
    $deleted = array();
    $this->query("SELECT b.id as bookid, d.name as basename, b.path||'/'||d.name as bookname, b.title FROM books b, data d WHERE b.id=d.book");
    $dbbooks = array();
    while ( $this->next_record() ) $dbbooks[] = array( 'id'=>$this->f('bookid'),'fullname'=>$this->f('bookname'),'title'=>$this->f('title'),'basename'=>$this->f('basename') );
    $fc = count($books); $bc = count($dbbooks);
    $GLOBALS['logger']->debug('   - There are $fc books on FS and $bc in the DB',$who);
    foreach ( $dbbooks as $book ) {
      if ( array_key_exists($GLOBALS['bookroot'].$book['fullname'],$books) ) continue; // this book still exists in FS and DB
      // if we are still here, this means the book was (re)moved in the file system. In case it was relocated, we first gather some info
      $GLOBALS['logger']->debug('   - book "'.$book['fullname']. '" has been (re)moved on file system level');
      $deleted[] = $book;
      $this->query("DELETE FROM books WHERE id=".$book['id']); // DB trigger takes care for dependencies
    }
    if ( empty($deleted) ) $GLOBALS['logger']->debug('   - Nothing (re)moved.',$who);
    else { $dc = count($deleted); $GLOBALS['logger']->debug('   - $dc books have been (re)moved on FS level.',$who); }
    $GLOBALS['logger']->info('    (re)moved: '.count($deleted),$who);
    return $deleted;
  }

}

?>