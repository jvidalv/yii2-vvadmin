<?php


namespace app\components;


use app\models\Article;
use app\models\ArticleHasAnchors;
use DOMDocument;
use DOMElement;
use DOMNode;
use phpDocumentor\Reflection\Types\Integer;
use Yii;

/**
 * Class ArticleParser
 * @package app\components
 * Parses article content
 */
class ArticleParser
{
    /**
     * @var DOMDocument
     */
    private $content;
    private $article_id;

    public $errors;

    /**
     * ArticleParser constructor.
     * @param $article_id
     * @param $content
     */
    public function __construct(integer $article_id, string $content)
    {
        libxml_use_internal_errors(true);
        $this->content = new DOMDocument();
        $this->article_id = $article_id;
        $this->content->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $this->sanitize();
    }

    /**
     * Checks for initial errors even before simple parsings
     */
    private function sanitize()
    {
        if (count(libxml_get_errors())) {
            foreach (libxml_get_errors() as $error) {
                switch ($error->code) {
                    case 513:
                        $this->addError('content', Yii::t('app', 'ID repetead at line {line}', ['line' => $error->line]));
                }
            }
            libxml_clear_errors();
        };
    }

    /**
     * @param $attr
     * @param $message
     */
    private function addError($attr, $message)
    {
        $this->errors[] = ['attr' => $attr, 'message' => $message];
    }

    /**
     * Returns content after all the modifications done by the class
     * @return string
     */
    public function getContent()
    {
        return $this->content->saveHTML();
    }

    /**
     * Delete all the anchors
     */
    private function deleteAnchors()
    {
        array_map(function (ArticleHasAnchors $val) {
            $val->delete();
        }, ArticleHasAnchors::findAll(['article_id' => $this->article_id]));
    }

    /**
     * For all the H2 in the DOM thas has as a first child and A
     */
    public function insertAnchors()
    {
        $this->deleteAnchors();
        // get all h2 in content
        $h2s = $this->content->getElementsByTagName('h2');
        foreach ($h2s as $i => $h2) {
            if ($h2->firstChild->tagName === 'a') {

                $h2->firstChild->setAttribute('id', "a$i");

                $anchor = new ArticleHasAnchors();
                $anchor->setAttributes([
                    'article_id' => $this->article_id,
                    'anchor_id' => "a$i",
                    'content' => $h2->nodeValue
                ]);
                $anchor->save();
            }
        };
    }

    /**
     * Checks if image was already parsed and does not need to be replaced
     * @param DOMNode $node
     * @return bool
     */
    private function imageHasToBeParsed(DOMNode $node)
    {
        return strpos($node->getAttribute('id'), 'img-') === false;
    }

    /**
     * loops throught all the imges and if the image has to be parsed it is uplodaded to the server
     */
    public function parseImatges()
    {
        $images = $this->content->getElementsByTagName('img');
        /**
         *  @var $img DOMNode
         */
        foreach ($images as $img)   {
            if(!$this->imageHasToBeParsed($img)) return;
            $data = $img->getAttribute('src');
            $tempPath = '';
        }
    }
}