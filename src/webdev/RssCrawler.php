<?php

namespace webdev;

use Httpful\Request;
use voku\cache\Cache;

/**
 * Class RssCrawler
 */
class RssCrawler
{
  /**
   * @param $url
   *
   * @return string
   */
  public function get($url)
  {
    $cache = new Cache(null, null, false, true);
    $cacheKey = 'rss-url:' . $url;

    if ($cache->getCacheIsReady() === true && $cache->existsItem($cacheKey)) {

      return $cache->getItem($cacheKey);

    } else {

      // get feed
      $rss = Request::get($url)
                    ->expectsHtml()
                    ->followRedirects(true)
                    ->send()
                    ->body;

      // save feed
      if ($rss) {
        $cache->setItem($cacheKey, $rss, 3600); // 60s * 60m = 3600s => 1h
      }

      return $rss;
    }
  }
}


