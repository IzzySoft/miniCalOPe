<?
 #############################################################################
 # miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
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

  //==========================================================[ DB Feeding ]===
  /** Truncate all tables. This is done prior to a new import.
   * @function truncAll
   */
  function truncAll() {
    $tables = array('comments','books','authors','tags','series','publishers',
                    'books_authors_link','books_publishers_link','books_ratings_link',
                    'books_series_link','books_tags_link','data');
    foreach($tables as $table) $this->query("DELETE FROM $table");
    $this->query('VACUUM');
  }

  /** Feed the genres into the database
   * @function make_genres
   * @param array genres array of strings
   */
  function make_genres($genres) {
    $i=0;
    foreach($genres AS $genre) {
      $this->query("INSERT INTO tags(id,name) VALUES ($i,'$genre')");
      ++$i;
    }
  }

  /** Feed the publisher into the database
   * @function make_publisher
   * @param array publisher array of strings
   */
  function make_publisher($publisher) {
    $i=0;
    $publisher = array_unique($publisher);
    foreach($publisher AS $genre) {
      $this->query("INSERT INTO publishers(id,name) VALUES ($i,'$genre')");
      ++$i;
    }
  }

  /** Feed the authors into the database
   * @function make_authors
   * @param array authors array of strings
   */
  function make_authors($authors) {
    $i=0;
    foreach($authors AS $author) {
      $this->query("INSERT INTO authors(id,name) VALUES ($i,'$author')");
      ++$i;
    }
  }

  /** Feed the series into the database
   * @function make_series
   * @param array series array of strings
   */
  function make_series($series) {
    $i=0;
    $series = array_unique($series);
    foreach($series as $serie) {
      $this->query("INSERT INTO series(id,name) VALUES ($i,'$serie')");
      ++$i;
    }
  }

  /** Feed the books into the database
   * @function make_books
   * @param array books array[string bookname] w/ props str lang, str genre, array tag, array author,
   *        str series, str series_index, str rating, str publisher, str isbn, array files[str type]=str filename
   */
  function make_books($books) {
    $b_id=0; $ba_id=0; $bt_id=0; $bs_id=0; $bp_id=0; $br_id=0; $c_id=0;
    foreach($books as $name=>$dummy) {
      $a_id=array(); $t_id=array();
      if ( !isset($books[$name]['files']) || !is_array($books[$name]['files']) ) {
        debugOut('! The book "'.$name.'" seems to have no files!');
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
      $this->query("INSERT INTO books(id,title,path,timestamp".$bf.") VALUES ($b_id,'$name','$path','".date('Y-m-d H:i:s',$books[$name]['lastmod'])."'".$bv.")");
      // relation to authors
      $anames = '';
      $books[$name]['author'] = array_unique($books[$name]['author']);
      foreach($books[$name]['author'] as $aut) $anames .= ",'".$aut."'";
      $this->query("SELECT id FROM authors WHERE name IN (".substr($anames,1).")");
      while ($this->next_record()) $a_id[] = $this->f('id');
      foreach($a_id as $aid) {
          $this->query("INSERT INTO books_authors_link (id,book,author) VALUES ($ba_id,$b_id,$aid)");
          ++$ba_id;
      }
      // relation to tags/genres
      $books[$name]['tag'][] = $books[$name]['genre'];
      $books[$name]['tag'] = array_unique($books[$name]['tag']);
      $tnames = '';
      foreach($books[$name]['tag'] as $aut) $tnames .= ",'".$aut."'";
      $this->query("SELECT id FROM tags WHERE name IN (".substr($tnames,1).")");
      while ($this->next_record()) $t_id[] = $this->f('id');
      foreach($t_id as $tid)  {
          $this->query("INSERT INTO books_tags_link(id,book,tag) VALUES($bt_id,$b_id,$tid)");
          ++$bt_id;
      }
      // relation to series
      if ( isset($books[$name]['series']) ) {
        $this->query("SELECT id FROM series WHERE name='".$books[$name]['series']."'");
        if ( $this->next_record() ) {
          $s_id = $this->f('id');
          $this->query("INSERT INTO books_series_link(id,book,series) VALUES ($bs_id,$b_id,$s_id)");
          ++$bs_id;
        }
      }
      // relation to publisher
      if ( isset($books[$name]['publisher']) ) {
        $this->query("SELECT id FROM publishers WHERE name='".$books[$name]['publisher']."'");
        if ( $this->next_record() ) {
          $p_id = $this->f('id');
          $this->query("INSERT INTO books_publishers_link(id,book,publisher) VALUES ($bp_id,$b_id,$p_id)");
          ++$bp_id;
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
  }

}

?>