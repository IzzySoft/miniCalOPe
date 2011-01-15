<?
 #############################################################################
 # miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################


class db extends DB_Sql {

  function __construct($dbfile) {
    $this->Database = $dbfile;
  }

  //==========================================================[ DB Feeding ]===
  /** Truncate all tables. This is done prior to a new import.
   * @function truncAll
   */
  function truncAll() {
    $tables = array('comments','ratings','series','publishers','books','authors','tags',
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

  /** Feed the books into the database
   * @function make_books
   * @param array books array[string bookname] w/ props str lang, str genre, str author, array files[str type]=str filename
   */
  function make_books($books) {
    $b_id=0; $ba_id=0; $bt_id=0; $c_id=0;
    foreach($books as $name=>$dummy) {
      foreach($books[$name]['files'] as $file) { // cannot address numerical - why?
        $pos = strrpos($file,DIRECTORY_SEPARATOR);
        $path = substr($file,strlen($GLOBALS['bookroot']),$pos-strlen($GLOBALS['bookroot']));
        break;
      }
      $this->query("INSERT INTO books(id,title,path,timestamp) VALUES ($b_id,'$name','$path','".date('Y-m-d H:i:s',$books[$name]['lastmod'])."')");
      $this->query("SELECT id FROM authors WHERE name='".$books[$name]['author']."'");
      $this->next_record(); $a_id = $this->f('id');
      $this->query("INSERT INTO books_authors_link (id,book,author) VALUES ($ba_id,$b_id,$a_id)");
      $this->query("SELECT id FROM tags WHERE name='".$books[$name]['genre']."'");
      $this->next_record(); $t_id = $this->f('id');
      $this->query("INSERT INTO books_tags_link(id,book,tag) VALUES($bt_id,$b_id,$t_id)");
      if ( !empty($books[$name]['desc']) ) {
        $this->query("INSERT INTO comments(id,book,text) VALUES($c_id,$b_id,'".$this->escape($books[$name]['desc'])."')");
        ++$c_id;
      }
      ++$b_id; ++$ba_id; ++$bt_id;
    }
  }

}

?>