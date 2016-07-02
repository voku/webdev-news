<?php

use paris\orm\Model;
use voku\db\DB;
use voku\helper\AntiXSS;

require_once __DIR__ . '/inc_globals.php';

// init
$db = DB::getInstance();
$antiXss = new AntiXSS();

$category = get('category', FILTER_SANITIZE_STRING);
$rss = get('rss', FILTER_SANITIZE_NUMBER_INT);
$text = get('text', FILTER_SANITIZE_NUMBER_INT);
$html = get('html', FILTER_SANITIZE_NUMBER_INT);

// --------------------------------------
// RSS
// --------------------------------------

if ($rss) {
  /* @var $rssModel \webdev\models\Rss */
  $rssModel = Model::factory('\webdev\models\Rss')
                   ->where_equal('id', $rss)
                   ->find_one();

  if (empty($rssModel)) {
    echo 'id not available';
    exit();
  }

  $media = $rssModel->getMedia();
  if ($media) {
    $mediaHtml = '<a href="' . $antiXss->xss_clean($media) . '">' . htmlentities($media) . '</a>';
  }

  $plaintextLink = '';
  if ($rssModel->getContentPlaintext()) {
    $plaintextLink = '<a href=index.php?rss=' . (int)$rssModel->getId() . '&text=1>paintext</a>';
  }

  $htmlLink = '';
  if ($rssModel->getContentHtml()) {
    $htmlLink = '<a href=index.php?rss=' . (int)$rssModel->getId() . '&html=1>html</a>';
  }

  $contentLinkHelper = '';
  if ($plaintextLink && $htmlLink) {
    $contentLinkHelper = ' | ';
  }

  echo '
  <h1>
    <a href="' . $antiXss->xss_clean($rssModel->getLink()) . '">
      ' . htmlentities($rssModel->getTitle()) . '
    </a>
  </h1>
  <span>
    ' . $mediaHtml . '
    ' . $antiXss->xss_clean($rssModel->getDescription()) . '
  </span>
  <br /><br />
  <p>
    ' . $plaintextLink . '
    ' . $contentLinkHelper . '
    ' . $htmlLink . '
  </p>
  ';

  if ($text == 1) {

    $contentPlaintext = $rssModel->getContentPlaintext();

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
          '<a href="' . $urlEncode . '">' . $urlEncode . '</a>',
          $contentPlaintext
      );
    }
    $contentPlaintext = urldecode($contentPlaintext);

    echo $antiXss->xss_clean(nl2br($contentPlaintext));
  }

  if ($html == 1) {
    echo $antiXss->xss_clean($rssModel->getContentHtml());
  }
}

// --------------------------------------
// OVERVIEW
// --------------------------------------

if (!$category) {
  $sql = "SELECT * FROM webdev_models_website
    GROUP BY category
    ORDER BY category
  ";
  $result = $db->query($sql);

  $htmlInner = '';
  foreach ($result->fetchAllArray() as $row) {
    $htmlInner .= '
    <li>
      <a href=index.php?category=' . urlencode($row['category']) . '>
        ' . htmlentities($row['category']) . '
      </a>
    </li>
    ';
  }

  echo '<hr /><nav><ul>' . $htmlInner . '</ul></nav>';
  exit();
}

// --------------------------------------
// CATEGORY
// --------------------------------------

$sql = "SELECT * FROM webdev_models_website as w
    LEFT JOIN webdev_models_rss as r
      ON r.website_id = w.id
    WHERE category = '" . $db->escape($category) . "'
    AND enabled = 1
    ORDER BY pub_date DESC
  ";
$result = $db->query($sql);

$htmlInner = '';
foreach ($result->fetchAllArray() as $row) {
  $htmlInner .= '
  <li>
    <a href=index.php?rss=' . (int)$row['id'] . '>
      ' . htmlentities($row['title']) . '
    </a>
    <span>
      ' . htmlentities(str_replace(array('http://', 'https://', '//'), '', $row['url'])) . ' | ' . htmlentities($row['pub_date']) . '
    </span>
  </li>';
}

echo '<nav><ul>' . $htmlInner . '</ul></nav>';
exit();