<?php

ini_set('memory_limit', -1);
ini_set("mysql.connect_timeout", -1);
ini_set('max_execution_time', -1);

use paris\orm\Model;
use webdev\OpmlToArrayy;
use webdev\RssCrawler;
use webdev\RssToArrayy;

require_once __DIR__ . '/inc_globals.php';

function saveFeedlyToDb()
{
  $opmlFeedly = new OpmlToArrayy(__DIR__ . '/data/feedly.opml');
  $rssCrawler = new RssCrawler();

  foreach ($opmlFeedly->getRssCategories() as $category => $items) {
    foreach ($items as $item) {

      /* @var $websiteModel \webdev\models\Website */
      $websiteModel = Model::factory('\webdev\models\Website')
                           ->where_equal('url', $item['url'])
                           ->find_one();

      if (empty($websiteModel)) {
        $websiteModel = Model::factory('\webdev\models\Website')->create();
        $websiteModel->setCategory($category);
        $websiteModel->setEnabled(1);
        $websiteModel->setUrl($item['url']);
        $websiteModel->setRssUrl($item['xml']);
        $websiteModel->save();
      }

      $rssString = $rssCrawler->get($item['xml']);
      $rss = new RssToArrayy($rssString);
      foreach ($rss->getRssArticles() as $rssItemTitle => $rssItem) {

        /* @var $rssModel \webdev\models\Rss */
        $rssModel = Model::factory('\webdev\models\Rss')
                         ->where_equal('link', (string)$rssItem['link'])
                         ->find_one();

        $created = false;
        if (empty($rssModel)) {
          $created = true;
          $rssModel = Model::factory('\webdev\models\Rss')->create();
          $rssModel->setCreated(new \DateTime('now'));
        }

        $updated = false;
        if (strtotime((string)$rssItem['pubDate']) > strtotime($rssModel->pub_date)) {
          $updated = true;
          $rssModel->setUpdated(new \DateTime('now'));
        }

        if (
            $created === true
            ||
            $updated === true
        ) {
          $rssModel->setTitle((string)$rssItemTitle);
          $rssModel->setLink((string)$rssItem['link']);
          $rssModel->setPubDate((string)$rssItem['pubDate']);
          $rssModel->setMedia((string)$rssItem['media']);
          $rssModel->setDescription((string)$rssItem['description']);
          $rssModel->setContentHtml((string)$rssItem['content_html']);
          $rssModel->setContentPlaintext((string)$rssItem['content_plaintext']);
          $rssModel->setWebsiteId($websiteModel->getId());
          $rssModel->save();

          // DEBUG
          $rssDump = $rssModel->as_array();
          dump($rssDump['link'], false);

        } else {
          continue;
        }
      }
    }
  }
}

saveFeedlyToDb();
