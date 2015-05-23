<?php
 #############################################################################
 # miniCalOPe                               (c) 2010-2015 by Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

require_once(dirname(__FILE__).'/common.php');
require_once(__DIR__.'/class.filefuncs.php');
$filefuncs = new filefuncs($logger,$use_markdown,$bookformats,$bookdesc_ext,$bookmeta_ext,$check_xml,$skip_broken_xml);

?>