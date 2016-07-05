<?php

namespace webdev;

use Arrayy\Arrayy;
use Stringy\Stringy;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;
use voku\Html2Text\Html2Text;

/**
 * Class Rss
 *
 * @package webdev
 */
class RssToArrayy
{
  /**
   * @var HtmlDomParser
   */
  private $dom;

  /**
   * @var Arrayy[]
   */
  private $rssArticles;

  /**
   * @var Html2Text
   */
  private $htmlToText;

  /**
   * Opml constructor.
   *
   * @param string $input
   */
  public function __construct($input)
  {
    if ($input) {
      $this->htmlToText = new Html2Text();
      $this->rssArticles = new Arrayy();

      $this->dom = HtmlDomParser::str_get_html($input);

      foreach ($this->dom->find('script, style') as $tag) {
        $tag->outertext = '';
      }

      $this->parse();
    }
  }

  private function parse()
  {
    $elements = $this->dom->getElementsByTagName('item');

    if (count($elements) === 0) {
      $elements = $this->dom->getElementsByTagName('entry');
    }

    foreach ($elements as $element) {

      // title
      $title = (string)$this->parseFindHelper($element, 'title');
      $this->rssArticles[$title] = new Arrayy();

      // init
      $this->rssArticles[$title]['link'] = new Stringy('');
      $this->rssArticles[$title]['media'] = new Stringy('');
      $this->rssArticles[$title]['content_html'] = new Stringy('');
      $this->rssArticles[$title]['content_plaintext'] = new Stringy('');
      $this->rssArticles[$title]['pubDate'] = new Stringy('');
      $this->rssArticles[$title]['description'] = new Stringy('');

      // link
      $href = $this->parseFindHelper($element, 'link', 'href');
      if (!(string)$href) {
        $href = $this->parseFindHelper($element, 'link');
      }
      if (!(string)$href) {
        $href = $this->parseFindHelper($element, 'guid');
      }
      $this->rssArticles[$title]['link'] = $href;

      // media e.g.: mp3
      $media = $this->parseFindHelper($element, 'enclosure', 'url'); // "<enclosure url=..."
      if (!(string)$media) {
        $media = $this->parseFindHelper($element, 'content', 'url'); // "<media:content url=..."
      }
      $this->rssArticles[$title]['media'] = $media;

      // content
      $content = $this->parseFindHelper($element, 'encoded'); // "<content:encoded>"
      if (!(string)$content) {
        $content = $this->parseFindHelper($element, 'content'); // "<content type="xhtml">"
      }
      $this->rssArticles[$title]['content_html'] = $content;
      $this->rssArticles[$title]['content_plaintext'] = $this->htmlToTextHelper($content);

      // date
      $date = $this->parseFindHelper($element, 'updated');
      if (!(string)$date) {
        $date = $this->parseFindHelper($element, 'published');
      }
      if (!(string)$date) {
        $date = $this->parseFindHelper($element, 'pubDate');
      }
      if (!(string)$date) {
        $date = $this->parseFindHelper($element, 'pubdate');
      }
      if (!(string)$date) {
        $date = $this->parseFindHelper($element, 'date'); // <dc:date>
      }
      $this->rssArticles[$title]['pubDate'] = $date;

      // description
      $description = $this->parseFindHelper($element, 'description');
      if (!(string)$description) {
        $description = Stringy::create($this->rssArticles[$title]['content_plaintext'])
                              ->shortenAfterWord(100, '...');
      }
      $this->rssArticles[$title]['description'] = $description;
    }

    // DEBUG
    //var_dump($this->rssArticles); exit();
  }

  /**
   * @param $html
   *
   * @return Stringy
   */
  private function htmlToTextHelper($html)
  {
    $this->htmlToText->setHtml($html);
    $text = $this->htmlToText->getText();
    $stringy = Stringy::create($text);

    return $this->cleanup($stringy);
  }

  /**
   * @param Stringy     $stringy
   * @param null|string $tag
   *
   * @return Stringy
   */
  private function cleanup(Stringy $stringy, $tag = null)
  {
    return $stringy->replaceAll(
        array(
            '<' . $tag . '>',
            '</' . $tag . '>',
            '<![CDATA[',
            '&lt;![CDATA[',
            ']]>',
            ']]&gt;',
        ),
        ''
    );
  }

  /**
   * @param SimpleHtmlDom $node
   * @param string        $find
   * @param string|null   $htmlAttribute
   *
   * @return mixed
   */
  private function parseFindHelper(SimpleHtmlDom $node, $find, $htmlAttribute = null)
  {
    if ($htmlAttribute !== null) {
      $text = Stringy::create($node->getElementByTagName($find)->$htmlAttribute);
    } else {
      $text = Stringy::create($node->getElementByTagName($find)->innerHtml());
    }

    return $this->cleanup($text, $find);
  }

  /**
   * get an array of all information from the "RSS"-input
   *
   * @return Arrayy[]
   */
  public function getRssArticles()
  {
    return $this->rssArticles;
  }
}
