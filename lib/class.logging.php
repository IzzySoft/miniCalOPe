<?php
#############################################################################
# phpApi                                   (c) 2004-2011 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
# ------------------------------------------------------------------------- #
# Logging (file & screen)                                                   #
#############################################################################
# $Id$

#============================================================[ Constants ]===
/** LogLevel Emergency
 * @constant integer EMERGENCY
 */
define('EMERGENCY',0);
/** LogLevel Critical
 * @constant integer CRITICAL
 */
define('ALERT',10);
/** LogLevel Alert
 * @constant integer ALERT
 */
define('CRITICAL',20);
/** LogLevel Error
 * @constant integer ERROR
 */
define('ERROR',30);
/** LogLevel Warning
 * @constant integer WARNING
 */
define('WARNING',40);
/** LogLevel Notice
 * @constant integer NOTICE
 */
define('NOTICE',50);
/** LogLevel Info
 * @constant integer INFO
 */
define('INFO',60);
/** LogLevel Debug
 * @constant integer DEBUG
 */
define('DEBUG',70);
/** Logging Disabled Level
 * @constant integer NOLOG
 */
define('NOLOG',-1);
/** Are we running in CLI mode (i.e. from command line, not browser)?
 * @constant boolean IS_CLI
 */
define ('IS_CLI', !( isset($_SERVER) && isset($_SERVER['REMOTE_ADDR']) ));

#======================================================[ The Logging Class ]===
/** Logging to file and screen
 * @package Api
 * @class logging
 * @author Izzy (izzysoft AT qumran DOT org)
 * @copyright (c) 2011-2011 by Itzchak Rehberg and IzzySoft
 */
class logging {

  /** Logfile Name
   * @class logging
   * @attribute private string logfile
   */
  private $logfile = '';
  /** Screen Log Level
   * @class logging
   * @attribute private screenlevel
   */
  private $screenlevel = INFO;
  /** File Log Level
   * @class logging
   * @attribute filelevel
   */
  private $filelevel = INFO;
  /** Error stack
   *  This is an array [0..n] of int(level), int(msg)
   * @class logging
   * @attribute array errors
   */
  public $errors = array();

  /** Class initialization
   * @constructor logging
   * @param optional string logname (default: '' = no file logging)
   * @param optional integer fileloglevel (default: INFO)
   * @param optional integer screenloglevel (default: INFO)
   */
  function __construct($fname='',$flevel=INFO,$slevel=INFO) {
    if ( empty($fname) ) {
      $this->setloglevel('file',NOLOG);
    } else {
      $this->setlogfile($fname);
      $this->setloglevel('file',$flevel);
    }
    $this->setloglevel('screen',$slevel);
    if (IS_CLI) {
      $this->scrnl = "\n";
    } else {
      $this->scrnl = "<BR>\n";
    }
  }

  /** Store processing errors
   *  Appends the specified error to the internal errors array
   * @function private _error
   * @param integer level Severity
   * @param string msg Error Message
   */
  function _error($level,$msg) {
    $this->errors[] = array('level'=>$level,'msg'=>$msg);
  }

  /** Setup the logfile
   *  If an error occurs here, it is logged into the logging::errors array and the method returns FALSE
   * @method setlogfile
   * @param string filename
   * @return boolean success
   */
  function setlogfile($fname) {
    if ( empty($fname) ) { $this->_error(ERROR,'setlogfile: No filename specified'); return FALSE; }
    if ( !is_dir( dirname($fname) ) ) { $this->_error(ERROR,"setlogfile: Directory of specified logfile '$fname' does not exist"); return FALSE; }
    if ( file_exists($fname) ) {
      if ( !is_writeable($fname) ) { $this->_error(ERROR,"setlogfile: Specified logfile '$fname' is not writeable for us"); return FALSE; }
    } else {
      if ( !is_writeable(dirname($fname)) ) { $this->_error(ERROR,"setlogfile: Specified logfile '$fname' does not exist and cannot be created due to lacking write permission to the directory"); return FALSE; }
    }
    $this->logfile = $fname;
    return TRUE;
  }

  /** Setup loglevels
   * @method public setloglevel
   * @param string type What log to alter: 'screen' or 'file'?
   * @param integer level Loglevel
   * @return boolean success
   */
  public function setloglevel($ltype='file',$llevel=INFO) {
    $type = strtolower($ltype);
    if ( !in_array($type,array('screen','file')) ) {
      $this->_error(ERROR,"setloglevel: log type must be one of 'screen','file' - you specified '$ltype'");
      return FALSE;
    }
    if ( !is_numeric($llevel) ) {
      $this->_error(ERROR,"setloglevel: log level must be integer - you specified '$llevel'");
      return FALSE;
    }
    switch ($type) {
      case 'screen' : $this->screenlevel = $llevel; break;
      case 'file'   : $this->filelevel   = $llevel; break;
      default: $this->_error(ERROR,'setloglevel: default in switch statement triggered???'); return FALSE; break;
    }
    return TRUE;
  }

  /** Log a message (generic)
   * @method public log
   * @param integer level Level for this message
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function log($level,$msg,$mod='') {
    if ( !is_numeric($level) ) {
      $this->_error(ERROR,"log: level must be integer, got '$level'");
      return FALSE;
    }
    if ( !is_string($mod) ) {
      $this->_error(WARNING,"log: Specified module name is not a string");
      $mod = '';
    }
    switch ($level) {
      case EMERGENCY: $llevel = 'EMERGENCY'; break;
      case ALERT    : $llevel = 'ALERT'; break;
      case CRITICAL : $llevel = 'CRITICAL'; break;
      case ERROR    : $llevel = 'ERROR'; break;
      case WARNING  : $llevel = 'WARNING'; break;
      case NOTICE   : $llevel = 'NOTICE'; break;
      case INFO     : $llevel = 'INFO'; break;
      case DEBUG    : $llevel = 'DEBUG'; break;
      default       : $llevel = $level; break;
    }
    if ( isset($_SERVER) && isset($_SERVER['REMOTE_ADDR']) ) $who = $_SERVER['REMOTE_ADDR'];
    else $who = 'local';
    if ( !empty($this->logfile) && $level <= $this->filelevel ) {
      error_log(date('Y-m-d H:i:s')." $who $llevel $mod $msg\n", 3, $this->logfile);
    }
    if ( $level <= $this->screenlevel ) {
      echo "$msg" . $this->scrnl;
    }
  }

  /** Log an emergency error
   *  (shortcut to logging::log)
   * @method public emergency
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function emergency($msg,$mod='') {
    $this->log(EMERGENCY,$msg,$mod);
  }

  /** Log an alert
   *  (shortcut to logging::log)
   * @method public alert
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function alert($msg,$mod='') {
    $this->log(ALERT,$msg,$mod);
  }

  /** Log a critical error
   *  (shortcut to logging::log)
   * @method public critical
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function critical($msg,$mod='') {
    $this->log(CRITICAL,$msg,$mod);
  }

  /** Log a "normal" error
   *  (shortcut to logging::log)
   * @method public error
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function error($msg,$mod='') {
    $this->log(ERROR,$msg,$mod);
  }

  /** Log a warning
   *  (shortcut to logging::log)
   * @method public warning
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function warning($msg,$mod='') {
    $this->log(WARNING,$msg,$mod);
  }

  /** Log a warning (alias to logging::warning)
   * @method public warn
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function warn($msg,$mod='') {
    $this->warning($msg,$mod);
  }

  /** Log a notice
   *  (shortcut to logging::log)
   * @method public notice
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function notice($msg,$mod='') {
    $this->log(NOTICE,$msg,$mod);
  }

  /** Log an informal message
   *  (shortcut to logging::log)
   * @method public info
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function info($msg,$mod='') {
    $this->log(INFO,$msg,$mod);
  }

  /** Log a debug message
   *  (shortcut to logging::log)
   * @method public debug
   * @param string msg Log message
   * @param optional string module Calling modul
   */
  public function debug($msg,$mod='') {
    $this->log(DEBUG,$msg,$mod);
  }

}

?>