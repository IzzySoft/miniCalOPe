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
   * @function truncAll
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function truncAll($who='SCAN') {
    $GLOBALS['logger']->info('  + Truncating Tables',$who);
    $tables = array('comments','books','authors','tags','series','publishers',
                    'books_authors_link','books_publishers_link','books_ratings_link',
                    'books_series_link','books_tags_link','data');
    foreach($tables as $table) $this->query("DELETE FROM $table");
    $this->query('VACUUM');
  }

  /** Feed the genres into the database
   * @function make_genres
   * @param array genres array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_genres($genres,$who='SCAN') {
    $genres = array_unique($genres);
    $GLOBALS['logger']->info('  + Inserting Tags ('.count($genres).')',$who);
    $i=0;
    $this->query('BEGIN TRANSACTION');
    foreach($genres AS $genre) {
      $this->query("INSERT INTO tags(id,name) VALUES ($i,'$genre')");
      ++$i;
    }
    $this->query('COMMIT TRANSACTION');
  }

  /** Feed the publisher into the database
   * @function make_publisher
   * @param array publisher array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_publisher($publisher,$who='SCAN') {
    $i=0;
    $publisher = array_unique($publisher);
    $GLOBALS['logger']->info('  + Inserting Publisher ('.count($publisher).')',$who);
    $this->query('BEGIN TRANSACTION');
    foreach($publisher AS $genre) {
      $genre = $this->escape($genre);
      if ( $this->query_nohalt("SELECT id FROM publishers WHERE name='$genre'") ) {
        if ( !$this->next_record() ) {
          $this->query("INSERT INTO publishers(id,name) VALUES ($i,'$genre')");
          ++$i;
        }
      } else {
        $GLOBALS['logger']->error("SQL Error for publisher [$genre] (".$this->Error.")",$who);
      }
    }
    $this->query('COMMIT TRANSACTION');
  }

  /** Feed the authors into the database
   * @function make_authors
   * @param array authors array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_authors($authors,$who='SCAN') {
    $authors = array_unique($authors);
    $GLOBALS['logger']->info('  + Inserting Authors ('.count($authors).')',$who);
    $i=0;
    $this->query('BEGIN TRANSACTION');
    foreach($authors AS $author) {
      $this->query("INSERT INTO authors(id,name) VALUES ($i,'$author')");
      ++$i;
    }
    $this->query('COMMIT TRANSACTION');
  }

  /** Feed the series into the database
   * @function make_series
   * @param array series array of strings
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_series($series,$who='SCAN') {
    $i=0;
    $series = array_unique($series);
    $GLOBALS['logger']->info('  + Inserting Series ('.count($series).')',$who);
    $this->query('BEGIN TRANSACTION');
    foreach($series as $serie) {
      $ser = $this->escape($serie);
      if ( $this->query_nohalt("SELECT id FROM series WHERE name='$ser'") ) {
        $this->query("INSERT INTO series(id,name) VALUES ($i,'$ser')");
        ++$i;
      } else {
        $GLOBALS['logger']->error("! SQL-Error processing series [$serie]: ".$this->Error);
      }
    }
    $this->query('COMMIT TRANSACTION');
  }

  /** Feed the books into the database
   * @function make_books
   * @param array books array[string bookname] w/ props str lang, str genre, array tag, array author,
   *        str title, str series, str series_index, str rating, str publisher, str isbn, array files[str type]=str filename
   * @param optional string who Calling mod (for logging) - defaults to SCAN
   */
  function make_books($books,$who='SCAN') {
    $GLOBALS['logger']->info('  + Inserting Books ('.count($books).')',$who);
    $b_id=0; $ba_id=0; $bt_id=0; $bs_id=0; $bp_id=0; $br_id=0; $c_id=0; $d_id=0;
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
      $bf = ''; $bv = '';
      foreach(array('isbn','uri') as $fn) if (isset($books[$name][$fn])) {
        $bf .= ",$fn"; $bv .= ",'".$books[$name][$fn]."'";
      }
      if (isset($books[$name]['series_index'])) { $bf .= ",series_index"; $bv .= ",".$books[$name]['series_index']; }
      if ( empty($books[$name]['title']) ) $btitle = $this->escape($name);
      else $btitle = $this->escape($books[$name]['title']);
      if ( !$this->query_nohalt("INSERT INTO books(id,title,path,timestamp".$bf.") VALUES ($b_id,'$btitle','$path','".date('Y-m-d H:i:s',$books[$name]['lastmod'])."'".$bv.")") ) {
        $GLOBALS['logger']->error('! Error inserting book "'.$name.'" (author: "'.$books[$name]['author'][0].'", genre "'.$books[$name]['genre'].'")',$who);
        $GLOBALS['logger']->error('! ('.$this->Error.')');
        continue;
      }
      // ebook files
      foreach($books[$name]['files'] as $var=>$val) {
        $this->query("INSERT INTO data(id,book,format,uncompressed_size,name) VALUES ($d_id,$b_id,'$var',".filesize($val).",'".$books[$name]['fbasename']."')");
        ++$d_id;
      }
      // relation to authors
      $anames = '';
      $books[$name]['author'] = array_unique($books[$name]['author']);
      foreach($books[$name]['author'] as $aut) $anames .= ",'".$aut."'";
      $this->query("SELECT id FROM authors WHERE name IN (".substr($anames,1).")");
      while ($this->next_record()) $a_id[] = $this->f('id');
      if ( is_array($a_id) ) {
          foreach($a_id as $aid) {
              $this->query("INSERT INTO books_authors_link (id,book,author) VALUES ($ba_id,$b_id,$aid)");
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
              $this->query("INSERT INTO books_tags_link(id,book,tag) VALUES($bt_id,$b_id,$tid)");
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
          $this->query("INSERT INTO books_series_link(id,book,series) VALUES ($bs_id,$b_id,$s_id)");
          ++$bs_id;
        }
      }
      // relation to publisher
      if ( isset($books[$name]['publisher']) ) {
        $publisher = $this->escape($books[$name]['publisher']);
        if ( $this->query_nohalt("SELECT id FROM publishers WHERE name='$publisher'") ) {
          if ( $this->next_record() ) {
            $p_id = $this->f('id');
            $this->query("INSERT INTO books_publishers_link(id,book,publisher) VALUES ($bp_id,$b_id,$p_id)");
            ++$bp_id;
          }
        } else {
          $GLOBALS['logger']->error("! SQL Error for publisher [".$books[$name]['publisher']."] (".$this->Error.")",'SCAN');
          $GLOBALS['logger']->error("! (book: '$name', author: '".$books[$name]['author'][0].", genre: '".$books[$name]['genre']."')",$who);
        }
      }
      // relation to ratings
      if ( !empty($books[$name]['rating']) ) {
        $this->query("INSERT INTO books_ratings_link(id,book,rating) VALUES ($br_id,$b_id,".$books[$name]['rating'].")");
        ++$br_id;
      }
      // Detailled description
      if ( !empty($books[$name]['desc']) ) {
        $this->query("INSERT INTO comments(id,book,text) VALUES($c_id,$b_id,'".$this->escape($books[$name]['desc'])."')");
        ++$c_id;
      }
      ++$b_id;
    }
    $this->query('COMMIT TRANSACTION');
    $GLOBALS['logger']->info("    (Inserted $d_id eBook files along)",$who);
  }

}

?>