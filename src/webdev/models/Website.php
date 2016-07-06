<?php

namespace webdev\models;

use idiorm\orm\ORM;
use paris\orm\Model;

/**
 * Website: with rss-feeds
 *
 * @property-read int    $id
 * @property-read string $url
 * @property-read string $rss_url
 * @property-read string $category
 * @property-read int    $enabled
 */
class Website extends Model
{
  /**
   * @return int
   */
  public function getId()
  {
    return (int)$this->id;
  }

  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }

  /**
   * @param string $string
   */
  public function setCategory($string)
  {
    $this->category = $string;
  }

  /**
   * @return string
   */
  public function getRssUrl()
  {
    return $this->rss_url;
  }

  /**
   * @param string $string
   */
  public function setRssUrl($string)
  {
    $this->rss_url = $string;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @param string $string
   */
  public function setUrl($string)
  {
    $this->url = $string;
  }

  /**
   * @param int|boolean $boolean
   */
  public function setEnabled($boolean)
  {
    $this->enabled = (int)$boolean;
  }

  /**
   * @return int
   */
  public function getEnabled()
  {
    return (int)$this->enabled;
  }

  /**
   * @return ORM
   */
  public function rssFeeds()
  {
    return $this->has_many('\webdev\models\Rss');
  }
}
