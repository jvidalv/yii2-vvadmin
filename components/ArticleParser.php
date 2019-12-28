<?php


namespace app\components;


use app\models\Article;
use app\models\ArticleHasAnchors;
use app\models\ArticleHasMedia;
use app\models\Media;
use app\models\MediaHasTables;
use DOMDocument;
use DOMElement;
use DOMNode;
use Exception;
use phpDocumentor\Reflection\Types\Integer;
use Yii;
use yii\helpers\Url;
use yii\imagine\Image;

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
    public function __construct(int $article_id, string $content)
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
     * Returns image array with image size as [width, height]
     * @param DOMNode $image
     * @param string $tempPath
     * @return array
     */
    private function getImageSize(DOMNode $image, string $tempPath)
    {
        $image_real_data = getimagesize($tempPath);
        $size = [];
        $size[0] = $image->getAttribute('width') ?? $image_real_data[0];
        $size[1] = $image->getAttribute('height') ?? $image_real_data[1];
        return $size;
    }

    /**
     * Bool true if image is valid
     * @param array $size
     * @return bool
     */
    private function checkImageSize(array $size){
        return isset($size[0]) && isset($size[1]) && $size[0] < 1000 && $size[1] < 1000;
    }

    /**
     * loops throught all the imges and if the image has to be parsed it is uplodaded to the server
     */
    public function parseImatges()
    {
        $images = $this->content->getElementsByTagName('img');
        /**
         * @var $img DOMNode
         */
        foreach ($images as $img) {
            // If the image is already parsed we end the function here
            if (!$this->imageHasToBeParsed($img)) return;

            $data = $img->getAttribute('src');
            $tempPath = Media::PATH_TO_TEMPORARY . time() . '.png';

            try {
                // Get the data from the source as base64
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
            } catch (Exception $e) {
                $this->addError('image_data', Yii::t('app', 'there is a problem uploading the image to our servers'));
                return;
            }

            // If the temporal image can't be saved we end here
            if (!file_put_contents($tempPath, $data)) return;

            $con = Yii::$app->db->beginTransaction();

            try {

                $size = $this->getImageSize($img, $tempPath);
                // Checks for all params set and not bigger than 1000
                if(!$this->checkImageSize($size)) throw new Exception( Yii::t('app', 'image in line {line} is to big (max 1000px widht, height)', ['line' => $img->getLineNo()]));
                // Generate new folders in case they are missing
                Media::generateFoldersByTableName('article_has_media');

                $articleHasMedia = new ArticleHasMedia();
                $articleHasMedia->article_id = $this->article_id;
                $articleHasMedia->save(false);

                $media = new Media();
                $media->setAttributes([
                    'path' => 'uploads/' . date("Y") . '/' . date("m") . '/article_has_media/',
                    'file_name' => 'article_has_media' . '-' . $articleHasMedia->id . '.png',
                    'titol' => $img->getAttribute('title') ?: null,
                    'user_id' => Yii::$app->user->identity->id,
                    'es_imatge' => 1,
                ]);
                $media->save();

                $articleHasMedia->media_id = $media->id;
                $articleHasMedia->save();

                $mediaHasTables = new MediaHasTables();
                $mediaHasTables->setAttributes([
                    'media_id' => $media->id,
                    'table_name' => 'article_has_media',
                    'table_id' => $articleHasMedia->id,
                ]);
                $mediaHasTables->save();

                // Generate the image and delete the one stored in the temp path
                Image::thumbnail($tempPath, $size[0], $size[1])
                    ->save($media->path . $media->file_name, ['quality' => 100]);
                unlink($tempPath);

                // Modifies dom node with new properties
                $img->setAttribute('id', 'img-' . $articleHasMedia->id);
                $img->setAttribute('src', Url::base(true) . '/' . Media::img($articleHasMedia->id, 'article_has_media', $size));
                $img->setAttribute('style', $img->getAttribute('style') . "width:$size[0]px;height:$size[1]px");
                $img->setAttribute('width', $size[0]);
                $img->setAttribute('height', $size[1]);
                $img->setAttribute('onerror', "this.class='not-found'");

                $con->commit();

            } catch (Exception $e) {

                $this->addError('image_size', $e->getMessage());
                $con->rollback();

            }

        }
    }
}