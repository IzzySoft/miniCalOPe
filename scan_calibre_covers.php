#!/usr/bin/php
<?
#############################################################################
# miniCalOPe                                    (c) 2010 by Itzchak Rehberg #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
# ------------------------------------------------------------------------- #
# Scan for covers and create symlinks                                       #
#############################################################################
# $Id$

$use_lang = 'cal';

require_once('./config.php');
require_once('./lib/common.php');
require_once('./lib/files.php');
require_once('./lib/db_sqlite3.php');
require_once('./lib/db.php');

$coverdir = $cover_base.DIRECTORY_SEPARATOR.$use_lang;
if ( !is_dir($cover_base) ) mkdir($cover_base);
if ( !is_dir($coverdir) ) mkdir($coverdir);
$filefuncs->prepareCalibreCovers($coverdir,$bookroot,$cover_mode,$dbfile);
?>