<?php

namespace webdev\models;

use idiorm\orm\ORM;
use paris\orm\Model;

/**
 * Website: with rss-feeds
 *
 * @property int    $id
 * @property string $url
 * @property string $rss_url
 * @property string $category
 * @property int    $enabled
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
