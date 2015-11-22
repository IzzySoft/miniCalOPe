<?php
// Generic setup for faster access to the Amazon API
require_once("amazon_simple_ads_class.php"); // Amazon ads
if ($ads_asap_webvertizer) require_once("class.webvertizer.php");       // local ads

$localAds = array();
$localAutoCrits = '';
$autoAdsCalled = FALSE;

/** Prepare autp-matched local ads
 * @package webvertizer
 * @function setAutoAds
 * @param str tag
 * @param str pagetype
 * @param opt str wildcard
 */
function setAutoAds($tag,$pagetype,$wildcard='regex') {
  // tag: string to match webvertizer's "page_cat" column. '<genre>' for genre, start|authors|search|genre|titles for the corresponding initial page
  // pagetype: initial|list|book
  // wildcard: regex|none ('none' for "initial pages", else 'regex' – or always use 'regex', permitting for "(genre|genre)" expressions?)
  $webvert = new webvertizer(strtolower($GLOBALS['ads_asap_webvertizer_domain'])); // sitename should match, of course ;)
  $GLOBALS['localAds'] = array_merge($GLOBALS['localAds'],$webvert->getItemsByAutoMatch($tag,$pagetype,3,$wildcard));
  $GLOBALS['localAutoCrits'] = "latag::${tag};latyp::${pagetype};lamatch::$wildcard";
  $GLOBALS['autoAdsCalled']  = TRUE;
}

/** Prepare advertizements based on given specifications
 * @package AmazonAds
 * @function getAds
 * @param string criteria CSV of key-value-pairs separated by double-colons, e.g. "asin::3645603115,3645602151;keywords::Android Speicherkarte SDKarte;prodgroup::Electronics;limit::5"
 * @return array ads[0..n] of array of strings url,title,img,price
 * @todo Adjust cachedate on merge
 */
function getAds($criteria) {

  //GLOBAL $page; // we need the defaul ads, maybe
  // Specifications for the API
  $public    = $GLOBALS['ads_asap_pubkey'];
  $private   = $GLOBALS['ads_asap_privkey'];
  $affiliate = $GLOBALS['amazonID'];
  $site      = $GLOBALS['ads_asap_country'];
  $validKeywords = array('asin','keywords','prodgroup','limit');

  // Setting up defaults
  $limit     = 3;
  $asin      = '';
  $keywords  = '';
  $prodgroup = '';

  // Evaluate the $specs
  $specs = explode(';',$criteria); // CSV
  if ( !empty($criteria) ) foreach ($specs as $spec) {
    $item = explode('::',$spec);
    if ( !in_array($item[0],$validKeywords) ) {
      trigger_error('getAds: invalid keyword "'.$item[0].'" (from '.$criteria.')', E_USER_WARNING);
      continue;
    }
    $$item[0] = $item[1];
  }

  // Our localAds have top priority as they're paid for in advance – so we deal with them first
  if ( !empty($GLOBALS['localAds']) ) {
    $lacount = count($GLOBALS['localAds']['items']);
    if ( $lacount > 1 ) shuffle($GLOBALS['localAds']['items']); // randomize item order
    if ( $lacount == $limit ) return $GLOBALS['localAds']; // exact count: done.
    elseif ( $lacount > $limit ) { // ouch, too much? That hurts :)
      trigger_error("getAds: We've got too many paid ads here... (".$GLOBALS['localAutoCrits'].")", E_USER_WARNING);
      return $GLOBALS['localAds'];
    }
  } else {
    $lacount = 0;
  }

  if ( empty($asin) && empty($keywords) ) { // no ads to retrieve without criteria, sorry
    if ($GLOBALS['autoAdsCalled']) {
      $dAds = $GLOBALS['page']->getDefaultAds();
      foreach ($dAds as $ad) {
        if ( $lacount == $limit ) break;
        $GLOBALS['localAds']['items'][] = $ad;
        ++$lacount;
      }
      return $GLOBALS['localAds'];
    }
    else {
      trigger_error("getAds: got neither ASIN nor keywords (from '$criteria')", E_USER_WARNING);
      return array();
    }
  }

  // Initialize the AmazonAPI
  $amazon = new AmazonAds($public, $private, $affiliate, $site);
  $res    = array(); $asinCount = 0;

  // ASINs have priority
  if ( empty($asin) ) {
    $res = $GLOBALS['localAds'];
  } else {
    $res1 = $amazon->getItemByAsin($asin);
    $res['cachedate'] = $res1['cachedate'];
    if ( !empty($res1) ) { // skip if Amazon-Request failed
      $res['items'] = array();
      $asinCount = count($res1['items']);
      if ( $asinCount + $lacount == $limit ) {
        if ($lacount==0) return $res1;
        foreach($GLOBALS['localAds']['items'] as $item) $res['items'][] = $item;
        foreach($res1['items'] as $item) $res['items'][] = $item;
        return $res;
      }
      elseif ( $asinCount + $lacount > $limit ) {
        $rand = array_rand($res1['items'],$limit - $lacount);
        $res2 = array( 'cachedate'=>$res1['cachedate'], 'items'=>array() );
        if ( is_array($rand) ) foreach ($rand as $r) $res2['items'][] = $res1['items'][$r];
        else $res2['items'][] = $res1['items'][$rand];
        if ($lacount==0) return $res2;
        foreach($GLOBALS['localAds']['items'] as $item) $res['items'][] = $item;
        foreach($res2['items'] as $item) $res['items'][] = $item;
        return $res;
      }
      if ($lacount==0) $res = $res1;
      else {
        foreach($GLOBALS['localAds']['items'] as $item) $res['items'][] = $item;
        foreach($res1['items'] as $item) $res['items'][] = $item;
      }
    }
  }

  // Still here? So we need a search
  $needed = $limit - $asinCount - $lacount;
  if ( empty($keywords) || empty($prodgroup) ) { // we cannot search without
    trigger_error("getAds: We need $needed more items, but have neither keyword nor productgroup (from '$criteria')", E_USER_WARNING);
    return $res;
  }
  // OK, we have something to do with:
  $res2 = $amazon->getItemsByKeyword($keywords,$prodgroup,$needed); // just pick the diff number
  if ( empty($res2['items']) ) { // nothing found
    return $res;
  }
  foreach ( $res2['items'] as $r ) $res['items'][] = $r; // append our new findings
  if ( empty($res['cachedate']) || $res2['cachedate'] < $res['cachedate'] ) { // fix cachedate
    $res['cachedate'] = $res2['cachedate'];
  }

  // Yo, we're done!
  return $res;
}

function getAdBlock($ads) {
  if ( !empty($ads['items']) ) {
    $tpl = new Template("tpl/html");
    $tpl->set_file(array("template"=>"asap3col.tpl"));
    $tpl->set_block('template','itemblock','item');
    $i = 0;
    foreach($ads['items'] as $item) {
      if ( !isset($item['title']) || empty($item['title']) ) continue; // no content
      if ( strlen($item['title']) > 400 ) $item['title'] = substr($item['title'],0,400) . '…';
      if ( !isset($item['price']) || empty($item['price']) || !preg_match('!(EUR|USD|GBP) [0-9\.\,]+!',$item['price']) ) $item['price'] = '';
      $tpl->set_var('url',str_replace('http:','https:',$item['url']));
      $tpl->set_var('title',strip_tags($item['title']));
      $tpl->set_var('desc',$item['title']);
      $tpl->set_var('img',$item['img']);
      if ( empty($item['price']) ) $tpl->set_var('price_info','');
      elseif ( isset($item['source']) && $item['source'] == 'local' ) {
        if ( preg_match('! 0[,.]00$!',$item['price']) ) $tpl->set_var('price_info','');
        else $tpl->set_var('price_info',$item['price']);
      } else {
        $tpl->set_var('price_info',$item['price']);
      }
      if (isset($item['is_premium']) && $item['is_premium']) $tpl->set_var('class','premium');
      else $tpl->set_var('class','standard');
      $tpl->parse('item','itemblock',$i);
      ++$i;
    }
    $disclaimer = str_replace('%cachedate%',$ads['cachedate'],$GLOBALS['ads_asap_disclaimer']);
    $tpl->set_var('cachedate',$disclaimer);
    $adblock = $tpl->parse('out','template');
  } else $adblock = '';
  return $adblock;
}
/* example usage:
$foo = getAds('asin::3645603115,3645602151;keywords::Android Speicherkarte SDKarte;prodgroup::Electronics;limit::3');
if ( !empty($foo['items']) ) {
  foreach($foo['items'] as $item) $page->addAd($item);
  $page->setAdCacheDateString('Stand: '.$foo['cachedate'].'; Preis & Verfügbarkeit können sich geändert haben.');
}
*/

?>