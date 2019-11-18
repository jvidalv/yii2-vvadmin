<?php

namespace app\controllers;

use app\components\VController;
use app\models\Article;
use app\models\ArticleHasAnchors;
use app\models\ArticleHasMedia;
use app\models\ArticleHasTags;
use app\models\ArticleSearch;
use app\models\Language;
use app\models\Media;
use app\models\MediaHasTables;
use app\models\Tag;
use DOMDocument;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends VController
{

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new Article();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        Yii::$app->user->identity->changeLanguage($model->language->code);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model = $this->parseArticleTags($model);
            $model = $this->parseArticleContent($model);

            if (!$model->errors && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'the changes have been saved!'));
            } else {
                 array_map(function ($string) {
                     Yii::$app->session->addFlash('danger', $string[0] . '<br/>');}, $model->errors);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * @param $id
     * @param $lang_code
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionNewTranslation($id, $lang_code)
    {
        $model = $this->findModel($id);
        $lang = Language::findOne(['code' => $lang_code]);

        if ($model && $lang && $model->translations && !$model->translations['article_' . $lang->code]) {

            $newArticle = new Article();
            $newArticle->attributes = $model->attributes;
            $newArticle->slug = null;
            $newArticle->title = "($lang->code) $newArticle->title";
            $newArticle->created_at = null;
            $newArticle->updated_at = null;
            $newArticle->language_id = $lang->code;
            $newArticle->translation_of = $id;

            if ($newArticle->save()) {

                // copy anchors
                foreach($model->articleHasAnchors as $anchor){
                    $an = new ArticleHasAnchors();
                    $an->article_id = $newArticle->id;
                    $an->anchor_id = $anchor->anchor_id;
                    $an->content = $anchor->content;
                    $an->save();
                }

                // copy tags
                foreach($model->articleHasTags as $tag){
                    $an = new ArticleHasTags();
                    $an->article_id = $newArticle->id;
                    $an->tag_id = $tag->id;
                    $an->save();
                }

                $model->translations['article_' . $lang->code] = $newArticle->id;
                $model->translations->save();
            }

            Yii::$app->user->identity->changeLanguage($lang_code);
            return $this->redirect(['update', 'id' => $newArticle->id]);
        }

        Yii::$app->session->setFlash('error', Yii::t('app', 'error'));
        return $this->redirect(['update', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * We parse the content article and apply all the modifications
     * We also capture errors
     * @param Article $model
     * @return Article
     */
    private function parseArticleContent(Article $model)
    {
        libxml_use_internal_errors(true);
        $content = new DOMDocument();
        $content->loadHTML($model->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        if (count(libxml_get_errors())) {
            foreach (libxml_get_errors() as $error) {
                switch ($error->code) {
                    case 513:
                        $model->addError('content', Yii::t('app', 'ID repetead at line {line}', ['line' => $error->line]));
                }
            }
            libxml_clear_errors();
            return $model;
        };

        array_map(function (ArticleHasAnchors $val) {
            $val->delete();
        }, ArticleHasAnchors::findAll(['article_id' => $model->id]));

        $links = $content->getElementsByTagName('a');
        foreach ($links as $link) {
            if (strpos($link->getAttribute('id'), "anchor-") !== false) {
                $anchor = new ArticleHasAnchors();
                $anchor->setAttributes([
                    'article_id' => $model->id,
                    'anchor_id' => $link->getAttribute('id'),
                    'content' => $link->parentNode->nodeValue
                ]);
                $anchor->save();
            };
        }

        $images = $content->getElementsByTagName('img');
        foreach ($images as $img) {
            if(strpos($img->getAttribute('id'), 'img-') === false)
            {
                $data = $img->getAttribute('src');

                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);

                $tempPath = Media::PATH_TO_TEMPORARY . time() . '.png';
                if(file_put_contents($tempPath, $data))
                {
                    $con = Yii::$app->db->beginTransaction();

                    try {

                        $imgC = getimagesize($tempPath);

                        if(isset($imgC[0]) && isset($imgC[1])){
                            if($imgC[0] > 1200 || $imgC[1] > 1200){
                                $model->addError('size', Yii::t('app', 'image in line {line} is to big (max 1200px widht, height)', ['line' => $img->getLineNo() ]));
                                throw new Exception(Yii::t('app', 'Error validating size, too big!'));
                            }
                        } else {
                            $model->addError('image', Yii::t('app', 'image in line {line} is not an image?', ['line' => $img->getLineNo() ]));
                            throw new Exception(Yii::t('app', 'error validating image'));
                        }

                        $table_name = 'article_has_media';
                        Media::generateFoldersByTableName($table_name);

                        $newA = new ArticleHasMedia();
                        $newA->article_id = $model->id;
                        $newA->save(false);

                        $newM = new Media();
                        $newM->setAttributes([
                            'path' => 'uploads/' . date("Y") . '/' . date("m") . '/' . $table_name . '/',
                            'file_name' => $table_name . '-' . $newA->id . '.png',
                            'titol' => $img->getAttribute('title') ?: null,
                            'user_id' => Yii::$app->user->identity->id,
                            'es_imatge' => 1,
                        ]);
                        $newM->save();

                        $newA->media_id = $newM->id;
                        $newA->save();

                        $newT = new MediaHasTables();
                        $newT->setAttributes([
                            'media_id' => $newM->id,
                            'table_name' => $table_name,
                            'table_id' => $newA->id,
                        ]);
                        $newT->save();

                        file_put_contents($newM->path . $newM->file_name, $data);
                        unlink($tempPath);

                        $img->setAttribute('id','img-' . $newA->id);
                        // todo cambiar a la api pa q xuto en public i privat
                        $sizes =  [
                            $img->getAttribute('width')  &&   $img->getAttribute('width') < 1201 ? $img->getAttribute('width') : $imgC[0],
                            $img->getAttribute('height')  &&   $img->getAttribute('height') < 1201 ? $img->getAttribute('height') : $imgC[1],
                        ];
                        $img->setAttribute('src', Url::to(['media/get-image', 'table' => $table_name, 'table_id' => $newA->id, 'size' => json_encode($sizes)]));

                        $con->commit();
                    } catch(Exception $e){
                        $model->addError('exception', $e);
                        $con->rollback();
                    }


                }
            }
        }

        // Save the modifications of dom
        $model->content = $content->saveHTML();

        return $model;
    }

    /**
     * Tag generation
     * @param Article $model
     * @return Article
     */
    private function parseArticleTags(Article $model)
    {
        if ($model->tags_form) {
            $tags = explode(',', $model->tags_form);
            array_map(function (ArticleHasTags $val) {
                $val->delete();
            }, ArticleHasTags::findAll(['article_id' => $model->id]));

            foreach ($tags as $tag) {

                $tag = trim($tag);
                $tagf = Tag::findOne(['name_' . $model->language->code => $tag]);
                if (!$tagf) {
                    $tagf = new Tag();
                    $tagf->setAttributes([
                        'name_ca' => $tag,
                        'name_es' => $tag,
                        'name_en' => $tag,
                        'priority' => 9,
                    ]);
                    $tagf->save();
                }

                $tagr = new ArticleHasTags();
                $tagr->setAttributes([
                    'tag_id' => $tagf->id,
                    'article_id' => $model->id,
                ]);

                $tagr->save();
            }
        }

        return $model;
    }

}
