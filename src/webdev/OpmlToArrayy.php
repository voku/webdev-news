<?php

namespace webdev;

use Arrayy\Arrayy;
use voku\helper\HtmlDomParser;

/**
 * Class Opml
 *
 * @package webdev
 */
class OpmlToArrayy
{
  /**
   * @var string
   */
  private $file;

  /**
   * @var HtmlDomParser
   */
  private $dom;

  /**
   * @var Arrayy
   */
  private $rssCategories;

  /**
   * Opml constructor.
   *
   * @param $file
   */
  public function __construct($file)
  {
    $this->file = $file;

    $this->rssCategories = new Arrayy();

    $this->dom = HtmlDomParser::file_get_html($file);

    $this->parse();
  }

  private function parse()
  {
    // init
    $categoryTitle = 'no-category';

    $body = $this->dom->getElementByTagName('body');
    foreach ($body->find('outline') as $element) {
      if (!$element->type) {
        $categoryTitle = $element->title;
        $this->rssCategories[$categoryTitle] = new Arrayy();
      } else {
        $rssTitle = $element->title;
        $this->rssCategories[$categoryTitle][$rssTitle] = new Arrayy();
        $this->rssCategories[$categoryTitle][$rssTitle]['xml'] = $element->xmlUrl;
        $this->rssCategories[$categoryTitle][$rssTitle]['url'] = $element->htmlUrl;
      }
    }
  }

  /**
   * get an array of all information from the "*.opml"-file
   *
   * @return Arrayy[]
   */
  public function getRssCategories()
  {
    return $this->rssCategories;
  }
}
