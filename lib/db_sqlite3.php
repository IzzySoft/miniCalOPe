<?php
###############################################################################
# Basic database methods for SQLite, compatible with the database classes of  #
# PHPLib                                                                      #
###############################################################################

/** Basic database methods for SQLite
 * @package Api
 * @class DB_Sql
 * @author Izzy (izzysoft AT qumran DOT org)
 * @copyright (c) 2009 by Itzchak Rehberg and IzzySoft
 */ 
class DB_Sql extends SQLite3 {
  var $Host     = ""; // Dummy for compatibility
  var $Database = ""; // Filename of the database file
  var $User     = ""; // Dummy for compatibility
  var $Password = ""; // Dummy for compatibility

  var $Link_ID  = 0;
  var $Query_ID = 0;
  var $Record   = array();
  var $Row;

  var $AdjustQuotes = TRUE; // replace escaped quotes by SQLite Quotes - e.g.
                            // "\'" by "''"? Needed to be conform with other DBs
  var $NumRowsEmulate = TRUE; // There is no num_rows for SQLite3. We can emulate
                            // it, but not very efficiently. If not emulated,
                            // num_rows() will simply return FALSE.

  var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore error, but spit a warning)

  var $Auto_Free = 0;
  var $PConnect  = 0;     // Dummy for compatibility
  var $Connected = FALSE;

  /** Initialization
   * @package Api
   * @constructor DB_Sql
   * @param optional string query query to run
   */
  function __construct($query = "") {
    if($query) {
      $this->query($query);
    }
  }

  /** Escape a string for safe insert
   * @function escape
   * @param string input
   * @return string escaped
   */
  function escape($str) {
    return $this->escapeString($str);
  }

  /** Connect to database
   * @class DB_Sql
   * @method connect
   * @return integer Link_ID on success, 0 otherwise
   */
  function connect() {
    if ( $this->Connected ) return;
    if ( empty($this->Database) ) {
      $this->halt("connect failed: no database specified");
    }
    $this->open($this->Database);
    $this->Connected = TRUE;
  }

  /** Disconnect from database
   * @class DB_Sql
   * @method disconnect
   */
  function disconnect() {
    if ( !$this->Connected ) return;
    $this->close();
    $this->Connected = FALSE;
  }

  /** Perform a query using the LIMIT clause
   * @method lim_query
   * @param string Query_String Query without limit clause
   * @param int start record to start at
   * @param int limit max number of records wanted
   * @return int allover records (w/o LIMIT used)
   */
  function lim_query($Query_String,$start,$limit) {
    $this->query($Query_String);
    $totals = $this->num_rows();
    $this->query($Query_String . ' LIMIT ' . $limit . ' OFFSET ' . $start);
    return $totals;
  }

  /** Perform a query
   * @class DB_Sql
   * @method query
   * @param string Query_String SQL Query
   * @return integer Query_ID on success, 0 otherwise
   */
  function query($Query_String) {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;
    $this->connect();
    //$this->qlog .= "$Query_String;\n";
    if ($this->AdjustQuotes) $Query_String = str_replace("\\'","''",str_replace('\\"','""',$Query_String));
    $this->Result = parent::query($Query_String);
    $this->Row   = 0;
    return $this->Result;
  }

  /** Execute a query (SELECT only!) and return all results as array[0..n] of assoc_array
   *  This emulates sqlite_array_query(dblink,query,SQLITE_ASSOC)
   * @method query_array
   * @param string query The SQL-Query to run
   * @return array results
   */
  function query_array($Query_String) {
    $ret = array();
    if ( $this->query($Query_String) ) {
      while ( $this->next_record() ) $ret[] = $this->Record;
    }
    return $ret;
  }

  /** Execute a query (SELECT only!) and return the first row of the result as an associative array.
   *  This emulates sqlite_single_query(dblink,query,TRUE)
   * @method query_single_row
   * @param string query The SQL-Query to run
   * @return array results
   */
  function query_single_row($Query_String) {
    if ( $this->query($Query_String) ) {
      $this->next_record();
      return $this->Record;
    } else {
      return array();
    }
  }

  /** Execute a query (SELECT only!) and return an array[0..n] of the first affected column.
   *  This emulates sqlite_single_query(dblink,query,FALSE)
   * @method query_single_column
   * @param string query The SQL-Query to run
   * @return array results (empty array if no results or if an error occures)
   */
  function query_single_column($Query_String) {
    if ( $this->query($Query_String) ) {
      $ret = array();
      while ( $this->next_record() ) {
        foreach ( $this->Record as $rec ) $ret[] = $rec;
      }
      return $ret;
    } else {
      return array();
    }
  }

  /** Retrieve a single value with one call.
   *  Intended for things like 'SELECT count(*)' or 'SELECT name .. WHERE id='
   * @method query_single_value
   * @param string query The SQL-Query to run
   * @return mixed result either the retrieved value or FALSE on error
   */
  function query_single_value($Query_String) {
    return $this->querySingle($Query_String);
  }

  /** Walk result set
   * @class DB_Sql
   * @method next_record
   * @return boolean success
   */
  function next_record() {
    $this->Record = $this->Result->fetchArray();
    $this->Row   += 1;
    $stat = is_array($this->Record);
    return $stat;
  }

  /** Position in result set
   * @class DB_Sql
   * @method seek
   * @param optional integer pos Position to set the pointer to (result/row number)
   * @return boolean success
   * @version not supported by current PHP API
   */
  function seek($pos) {
    return FALSE;
  }

  /** Obtain table metadata
   * @method metadata
   * @param string table_name
   * @return array 0..n of array(table,name,type,len,position,flags)
   * @version the field position is only kept for compatibility and will always
   *  be empty. Flags may contain attributes such as "PRIMARY KEY", if they
   *  immediately followd the column description. This code is still experimental
   *  and may fail, listing constraints as columns. Please let me know the
   *  details if you encounter this, so the code can be updated and fixed.
   */
  function metadata($table) {
    $count = 0;
    $i     = 0;
    $res   = array();

    $this->connect(); 
    $this->query("SELECT sql FROM sqlite_master WHERE name='$table' AND type='table'");
    if (!$this->next_record()) return FALSE; // no data found

    $sql = $this->f('sql');
    $pos_s = strpos($sql,"(") +1;
    $pos_e = strrpos($sql,")");
    $sql = substr($sql,$pos_s,$pos_e - $pos_s);
    $arr = explode(",",$sql);
    foreach($arr as $var) {
      if (strpos($sql,'UNIQUE')===0) continue; // constraint
      $var = str_replace("("," ",str_replace(")","",$var));
      $col = explode(" ",$var);
      $res[$i]["table"] = $table;
      $res[$i]["name"]  = $col[0];
      $res[$i]["type"]  = $col[1];
      $res[$i]["flags"] = "";
      $res[$i]["position"] = "";
      if (is_numeric($col[2])) {
        $res[$i]["len"] = $col[2];
        $flag_pos         = 3;
      } else {
        $flag_pos       = 2;
        $res[$i]["len"] = ""; // constraints like 'PRIMARY KEY' may follow the column name
      }
      while (!empty($col[$flag_pos])) {
        $res[$i]["flags"] .= " ".$col[$flag_pos];
        ++$flag_pos;
      }
      $res[$i]["flags"] = trim($res[$i]["flags"]);
      ++$i;
    }
    $this->free();
    return $res;
  }

  /** Free result set
   * @method free
   */
  function free() {
    $this->Result->finalize();
  }

  /** Evaluate the result for DML operation
   * @class DB_Sql
   * @method affected_rows
   * @return integer affected rows
   */
  function affected_rows() {
    return $this->changes();
  }

  /** Evaluate the result for SELECT operation (row count)
   * @class DB_Sql
   * @method num_rows
   * @return integer number of rows in result set
   * @version not available in PHP API, so this always returns FALSE
   */
  function num_rows() {
    if (!$this->NumRowsEmulate) return FALSE;
    $i = 0;
    while ($this->next_record()) ++$i;
    $this->Result->reset();
    return $i;
  }

  /** Evaluate the result for SELECT operation (fieldset count)
   * @class DB_Sql
   * @method num_fields
   * @return integer number of columns in result set
   */
  function num_fields() {
    return $this->Result->numColumns();
  }

  function nf() {
    return $this->num_rows();
  }

  function np() {
    print $this->num_rows();
  }

  /** Retrieve the content of a field in the current record of the result set
   * @class DB_Sql
   * @method f
   * @return string content of field
   */
  function f($Name) {
    return $this->Record[$Name];
  }

  function p($Name) {
    print $this->Record[$Name];
  }

  function halt($msg) {
    if ("no" == $this->Halt_On_Error)
      return;

    $this->haltmsg($msg);

    if ("report" != $this->Halt_On_Error)
      die("Session halted.");
  }

  function haltmsg($msg) {
    //file_put_contents('qlog.err',$this->qlog);
    printf("<p><b>Database error:</b> %s<br>\n", $msg);
    printf("<b>SQLite3 Error</b> %s</p>\n", $this->Error);
    $bt = debug_backtrace();
    $btl = count($bt);
    echo("<P><b>Backtrace:</b></P><pre>");
    for ($i=2;$i<=$btl;++$i) { // 0=here, 1=query which is already reported above
      echo "  [".$bt[$i]['file'].":".$bt[$i]['line']."] in function '".$bt[$i]['function']."()'\n";
    }
    echo "</pre>\n";
  }

  /** Retrieve all table names
   * @class DB_Sql
   * @method table_names
   * @return array table_name, tablespace_name (=Database), database
   */
  function table_names() {
    $this->query("SELECT name FROM SQLITE_MASTER WHERE type='table' ORDER BY name");
    $i=0;
    while ($info=sqlite3_fetch_array($this->Query_ID))
     {
      $return[$i]["table_name"]= $info[0];
      $return[$i]["tablespace_name"]=$this->Database;
      $return[$i]["database"]=$this->Database;
      $i++;
     }
   return $return;
  }

  /** Table locking
   * @class DB_Sql
   * @method lock
   * @param mixed table table to lock (either string or array of strings)
   * @param optional string mode locking mode (defaults to "write")
   * @return boolean success
   */
  function lock($table, $mode="write") {
    $this->connect();

    $query="lock tables ";
    if (is_array($table)) {
      while (list($key,$value)=each($table)) {
        if ($key=="read" && $key!=0) {
          $query.="$value read, ";
        } else {
          $query.="$value $mode, ";
        }
      }
      $query=substr($query,0,-2);
    } else {
      $query.="$table $mode";
    }
    $res = @sqlite3_query($this->Link_ID,$query);
    if (!$res) {
      $this->halt("lock($table, $mode) failed.");
      return 0;
    }
    return $res;
  }

  /** Unlock all tables
   * @class DB_Sql
   * @method unlock
   * @return boolean success
   */
  function unlock() {
    $this->connect();
    $res = @sqlite3_query($this->Link_ID,"unlock tables");
    if (!$res) {
      $this->halt("unlock() failed.");
      return 0;
    }
    return $res;
  }

}
?>
