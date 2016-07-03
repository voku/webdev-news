<?php

namespace webdev\models;

use idiorm\orm\ORM;
use paris\orm\Model;

/**
 * Rss-Feed
 *
 * @property int    $id
 * @property string $created
 * @property string $updated
 * @property string $pub_date
 * @property string $media
 * @property string $link
 * @property string $title
 * @property string $description
 * @property string $content_html
 * $property string $content_plaintext
 * @property int    $website_id
 */
class Rss extends Model
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
  public function getUpdated()
  {
    return $this->updated;
  }

  /**
   * @param \DateTime|string $created
   */
  public function setUpdated($created)
  {
    if ($created instanceof \DateTime) {
      $this->updated = $created->format('Y-m-d H:i:s');
    } else {
      $this->updated = $created;
    }
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }

  /**
   * @param \DateTime|string $created
   */
  public function setCreated($created)
  {
    if ($created instanceof \DateTime) {
      $this->created = $created->format('Y-m-d H:i:s');
    } else {
      $this->created = $created;
    }
  }

  /**
   * @return string
   */
  public function getPubDate()
  {
    return $this->pub_date;
  }

  /**
   * @param string $pub_date
   */
  public function setPubDate($pub_date)
  {
    if ($pub_date instanceof \DateTime) {
      $this->pub_date = $pub_date->format('Y-m-d H:i:s');
    } else {
      $this->pub_date = date(
          'Y-m-d H:i:s',
          strtotime(
              trim(
                  preg_replace(
                      "/(.*)( \+?-?[\d]{4}| \+?-?[\d]{2}:[\d]{2})$/",
                      '$1',
                      (string)$pub_date
                  )
              )
          )
      );
    }
  }

  /**
   * @return string
   */
  public function getMedia()
  {
    return $this->media;
  }

  /**
   * @param string $media
   */
  public function setMedia($media)
  {
    $this->media = $media;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }

  /**
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getContentHtml()
  {
    return $this->content_html;
  }

  /**
   * @param string $content_html
   */
  public function setContentHtml($content_html)
  {
    $this->content_html = $content_html;
  }

  /**
   * @return string
   */
  public function getContentPlaintext()
  {
    return $this->content_plaintext;
  }

  /**
   * @param string $content_plaintext
   */
  public function setContentPlaintext($content_plaintext)
  {
    $this->content_plaintext = $content_plaintext;
  }

  /**
   * @return int
   */
  public function getWebsiteId()
  {
    return $this->website_id;
  }

  /**
   * @param int $id
   */
  public function setWebsiteId($id)
  {
    $this->website_id = (int)$id;
  }


  /**
   * @return ORM
   */
  public function website()
  {
    return $this->has_one('\webdev\models\Website');
  }

}
