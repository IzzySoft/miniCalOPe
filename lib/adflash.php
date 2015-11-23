<?php
// Amazon Flash Ads

function getFlashAd($amazonID,$booktitle,$author,$booktags) {
    $tpl = new Template("tpl/html");
    $tpl->set_file(array("template"=>"flashads.tpl"));
    $tpl->set_var('amazonID',$amazonID);
    $tpl->set_var('amazon_bordercolor',$GLOBALS['ads_bordercolor']);
    $tpl->set_var('amazon_logocolor',$GLOBALS['ads_logocolor']);
    $tpl->set_var('booktitle_urlenc',urlencode($booktitle));
    $tpl->set_var('authorname_urlenc',urlencode($author));
    $tpl->set_var('booktags_urlenc',urlencode(str_replace(', ',';',$booktags)));
    return $tpl->parse('out','template');
}
?>