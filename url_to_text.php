<?php

use Httpful\Request;
use voku\helper\AntiXSS;
use voku\helper\HtmlDomParser;
use voku\Html2Text\Html2Text;

require_once __DIR__ . '/inc_globals.php';

// init
$htmlFromUrl = '';
$antiXss = new AntiXSS();

$url = get('url', FILTER_SANITIZE_STRING);

if (!is_url($url)) {
  echo 'no url';
  exit();
}

// get html
// get feed
try {
  $htmlFromUrl = Request::get($url)
                ->expectsHtml()
                ->followRedirects(true)
                ->send()
      ->body;
} catch (\Exception $e) {
  echo "error: " . $e->getMessage();
}

if (!$htmlFromUrl) {
  echo 'no content from: ' . $url;
  exit();
}

// remove: some tags
$htmlTmp = HtmlDomParser::str_get_html($htmlFromUrl);
foreach ($htmlTmp->find('nav, script, style, #sidebar, .screen-reader-text') as $htmlPart) {
  $htmlPart->outertext = '';
}

$tmpHtml = $htmlTmp->save();

// try to get the main <article>
$articleTmp = $htmlTmp->find('#article');
if (count($articleTmp) === 1) {
  $tmpHtml = $articleTmp->outertext;
}
$articleTmp = $htmlTmp->find('article');
if (count($articleTmp) === 1) {
  $tmpHtml = $articleTmp->outertext;
}

// html to text
$contentPlaintext = new Html2Text($tmpHtml, false, array('directConvert' => true));
$contentPlaintext = $contentPlaintext->getText();

// replace links
$urlsLength = array();
$urls = array();
$urlsTmp = getUrlsFromString($contentPlaintext);
foreach ($urlsTmp as $url) {
  $urls[] = $url;
  $urlsLength[] = strlen($url);
}

arsort($urlsLength);

foreach ($urlsLength as $key => $urlLength) {
  $urlEncode = urlencode($urls[$key]);
  $contentPlaintext = str_replace(
      $urls[$key],
      '<a target="_blank" href="' . $urlEncode . '">' . $urlEncode . '</a>',
      $contentPlaintext
  );
}
$contentPlaintext = urldecode($contentPlaintext);

// text to html
echo $antiXss->xss_clean(nl2br($contentPlaintext));
