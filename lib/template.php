<?php
 #############################################################################
 # phpVideoPro                              (c) 2001-2007 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 # ------------------------------------------------------------------------- #
 # Template class taken from PHPLIB                                          #
 # (C) Copyright 1999-2000 NetUSE GmbH                                       #
 #                    Kristian Koehntopp                                     #
 #############################################################################

 /* $Id$ */

 /** Template processing
  * @package Api
  * @class Template
  */

class Template {
  var $classname = "Template";

  # if set, echo assignments
  var $debug     = false;

  # $file[handle] = "filename";
  var $file  = array();

  # relative filenames are relative to this pathname
  var $root   = "";

  # $varkeys[key] = "key"; $varvals[key] = "value";
  var $varkeys = array();
  var $varvals = array();

  # "remove"  => remove undefined variables
  # "comment" => replace undefined variables with comments
  # "keep"    => keep undefined variables
  var $unknowns = "remove";

  # "yes" => halt, "report" => report error, continue, "no" => ignore error quietly
  var $halt_on_error  = "yes";

  # last error message is retained here
  var $last_error     = "";


  #############################################################################
  /** Initialization
   * @package Api
   * @constructor Template
   * @param optional string root template directory
   * @param optional string unknowns how to handle unknown variables (remove/comment/keep)
   */
  function Template($root = ".", $unknowns = "remove") {
    GLOBAL $pvp,$base_path,$base_url,$page_id;
    $this->set_root($root);
    $this->set_unknowns($unknowns);
    $this->set_var("base_dir",$base_url."/");
    $this->set_var("tpl_dir",str_replace($base_path,$base_url,$pvp->tpl_dir)."/");
    $this->set_var("logoff_link",$base_url."login.php?logout=1");
    $this->set_var("page_id",$page_id);
    $this->set_var("help_link","open_help('$page_id')");
    if (isset($pvp->link) && method_exists($pvp->link,"slink")) { // not needed for help system
      $this->set_var("home_link",$pvp->link->slink($base_url."index.php"));
      $this->set_var("search_link",$pvp->link->slink($base_url."search.php"));
    }
    if (function_exists("lang")) {
      $this->set_var("home_title",lang("start_page"));
      $this->set_var("search_title",lang("search_movie"));
      $this->set_var("help_title",lang("help"));
      $this->set_var("logoff_title",lang("login"));
#      if ($pvp->preferences->get("bubble_help_enable")) {
      if (method_exists($pvp->preferences,"get") && $pvp->preferences->get("bubble_help_enable")) {
        $self = dirname($_SERVER["PHP_SELF"]);
        $pos  = strrpos($self,"/");
        if (substr($self,$pos+1)=="help") {
          $this->set_var("btn_index",lang("index"));
          $this->set_var("btn_back",lang("back"));
          $this->set_var("btn_close",lang("close"));
        } else {
          $this->set_var("home_title",lang("start_page"));
          $this->set_var("search_title",lang("search"));
          $this->set_var("help_title",lang("help"));
          $this->set_var("logoff_title",lang("login"));
        }
      } // end bubble help
    }
  }

  /** Set a new template directory
   * @package Api
   * @class Template
   * @method set_root
   * @param string root new template directory
   * @return boolean success
   */  
  function set_root($root) {
    if (!is_dir($root)) {
      $this->halt("set_root: $root is not a directory.");
      return false;
    }
    $this->root = $root;
    return true;
  }

  /** Alter handling of unknown variables
   * @package Api
   * @class Template
   * @method set_unknowns
   * @param optional string unknowns (remove//comment/keep, defaults to keep)
   */
  function set_unknowns($unknowns = "keep") {
    $this->unknowns = $unknowns;
  }

  /** Define the template file to use
   * @package Api
   * @class Template
   * @method set_file
   * @param mixed handle handle for a filename (string) or array of handle,filename
   * @param optional string filename name of template file
   */
  function set_file($handle, $filename = "") {
    if (!is_array($handle)) {
      if ($filename == "") {
        $this->halt("set_file: For handle $handle filename is empty.");
        return false;
      }
      $this->file[$handle] = $this->filename($filename);
    } else {
      reset($handle);
      while(list($h, $f) = each($handle)) {
        $this->file[$h] = $this->filename($f);
      }
    }
  }

  /** Define a block inside the template
   *  (extract the template $handle from $parent, place variable {$name} instead)
   * @package Api
   * @class Template
   * @method set_block
   * @param string parent handle of parent block
   * @param string handle block to define
   * @param optional string name
   */
  function set_block($parent, $handle, $name = "") {
    if (!$this->loadfile($parent)) {
      $this->halt("subst: unable to load $parent.");
      return false;
    }
    if ($name == "")
      $name = $handle;

    $str = $this->get_var($parent);
    $reg = "/<!--\s+BEGIN $handle\s+-->(.*)\n\s*<!--\s+END $handle\s+-->/sm";
    preg_match_all($reg, $str, $m);
    $str = preg_replace($reg, "{" . "$name}", $str);
    $this->set_var($handle, $m[1][0]);
    $this->set_var($parent, $str);
  }

  /** define the content of one {var} inside the template
   *  Alternatively to passing two strings, they can be passed as a array
   *  of pairs name,value
   * @package Api
   * @class Template
   * @method set_var
   * @param string varname name of a variable that is to be defined
   * @param optional string value value of that variable
   */
  function set_var($varname, $value = "") {
    if (!is_array($varname)) {
      if (!empty($varname))
        if ($this->debug) print "scalar: set *$varname* to *$value*<br>\n";
        $this->varkeys[$varname] = "/".$this->varname($varname)."/";
        $this->varvals[$varname] = $value;
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        if (!empty($k))
          if ($this->debug) print "array: set *$k* to *$v*<br>\n";
          $this->varkeys[$k] = "/".$this->varname($k)."/";
          $this->varvals[$k] = $v;
      }
    }
  }

  /** Substitute variables
   * @package Api
   * @class Template
   * @method subst
   * @param string handle handle of template where variables are to be substituted
   * @return string parsed template (or FALSE on error)
   */
  function subst($handle) {
    if (!$this->loadfile($handle)) {
      $this->halt("subst: unable to load $handle.");
      return false;
    }
    $str = $this->get_var($handle);
    $str = @preg_replace($this->varkeys, $this->varvals, $str);
    return $str;
  }

  /** Substitute variables
   * @package Api
   * @class Template
   * @method psubst
   * @param string handle handle of template where variables are to be substituted
   * @return boolean FALSE
   */
  function psubst($handle) {
    print $this->subst($handle);
    return false;
  }

  /** Parse a template
   * @package Api
   * @class Template
   * @method parse
   * @param string target handle of variable to generate
   * @param mixed handle handle of template(s) to substitute (string or array)
   * @param optional boolean append append to target handle
   * @return string parsed template
   */
  function parse($target, $handle, $append = false) {
    if (!is_array($handle)) {
      $str = $this->subst($handle);
      if ($append) {
        $this->set_var($target, $this->get_var($target) . $str);
      } else {
        $this->set_var($target, $str);
      }
    } else {
      reset($handle);
      while(list($i, $h) = each($handle)) {
        $str = $this->subst($h);
        $this->set_var($target, $str);
      }
    }
    return $str;
  }

  /** Parse a template (final parse)
   *  calls parse(), prints the result and then returns FALSE
   * @package Api
   * @class Template
   * @method pparse
   * @param string target handle of variable to generate
   * @param mixed handle handle of template(s) to substitute (string or array)
   * @param optional boolean append append to target handle
   * @return boolean FALSE
   */
  function pparse($target, $handle, $append = false) {
    print $this->finish( $this->parse($target, $handle, $append) );
    return false;
  }

  /** Retrieve all set vars
   * @package Api
   * @class Template
   * @method get_vars()
   * @return array array of variables (keys)
   */
  function get_vars() {
    reset($this->varkeys);
    while(list($k, $v) = each($this->varkeys)) {
      $result[$k] = $this->varvals[$k];
    }
    return $result;
  }

  /** Retrieve variable contents
   * @package Api
   * @class Template
   * @method get_var
   * @param mixed varname name of variable (string) or array of variable names
   * @return mixed variable setting (string or array of contents)
   */
  function get_var($varname) {
    if (!is_array($varname)) {
      return $this->varvals[$varname];
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        $result[$k] = $this->varvals[$k];
      }
      return $result;
    }
  }

  /** Obtain list of undefined variables
   * @package Api
   * @class Template
   * @method get_undefined
   * @param string handle handle of a template.
   * @return array array of undefined variables (FALSE if none)
   */
  function get_undefined($handle) {
    if (!$this->loadfile($handle)) {
      $this->halt("get_undefined: unable to load $handle.");
      return false;
    }
    preg_match_all("/\{([^}]+)\}/", $this->get_var($handle), $m);
    $m = $m[1];
    if (!is_array($m))
      return false;

    reset($m);
    while(list($k, $v) = each($m)) {
      if (!isset($this->varkeys[$v]))
        $result[$v] = $v;
    }

    if (count($result))
      return $result;
    else
      return false;
  }

  /** Finnish a block (handle unknowns)
   * @package Api
   * @class Template
   * @method finish
   * @param string str string to finish.
   * @return string finnished string (parsed template)
   */
  function finish($str) {
    switch ($this->unknowns) {
      case "keep": break;
      case "remove": $str = preg_replace('/{[^ \t\r\n}]+}/', "", $str);
                     break;
      case "comment": $str = preg_replace('/{([^ \t\r\n}]+)}/', "<!-- Template $handle: Variable \\1 undefined -->", $str);
                     break;
    }
    return $str;
  }

  /** Print a Variable (first call finnish() on it and then print the result)
   * @package Api
   * @class Template
   * @method p
   * @param string varname name of variable to print
   */
  function p($varname) {
    print $this->finish($this->get_var($varname));
  }

  /** Retrieve a Variable (first call finnish() on it and return print the result)
   * @package Api
   * @class Template
   * @method get
   * @param string varname variable to retrieve
   * @return string finnished content
   */
  function get($varname) {
    return $this->finish($this->get_var($varname));
  }

  #############################################################################
  /* private: filename($filename)
   * filename: name to be completed.
   */
  function filename($filename) {
    if (substr($filename, 0, 1) != "/") {
      $filename = $this->root."/".$filename;
    }

    if (!file_exists($filename))
      $this->halt("filename: file $filename does not exist.");

    return $filename;
  }

  /* private: varname($varname)
   * varname: name of a replacement variable to be protected.
   */
  function varname($varname) {
    return preg_quote("{".$varname."}");
  }

  /* private: loadfile(string $handle)
   * handle:  load file defined by handle, if it is not loaded yet.
   */
  function loadfile($handle) {
    if (isset($this->varkeys[$handle]) and !empty($this->varvals[$handle]))
      return true;

    if (!isset($this->file[$handle])) {
      $this->halt("loadfile: $handle is not a valid handle.");
      return false;
    }
    $filename = $this->file[$handle];

    $str = implode("", @file($filename));
    if (empty($str)) {
      $this->halt("loadfile: While loading $handle, $filename does not exist or is empty.");
      return false;
    }

    $this->set_var($handle, $str);

    return true;
  }

  #############################################################################
  /* public: halt(string $msg)
   * msg:    error message to show.
   */
  function halt($msg) {
    $this->last_error = $msg;

    if ($this->halt_on_error != "no")
      $this->haltmsg($msg);

    if ($this->halt_on_error == "yes")
      die("<b>Halted.</b>");

    return false;
  }

  /* public, override: haltmsg($msg)
   * msg: error message to show.
   */
  function haltmsg($msg) {
    printf("<b>Template Error:</b> %s<br>\n", $msg);
  }
}
?>
