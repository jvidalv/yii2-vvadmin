<?php

namespace app\controllers;

use app\components\VController;
use app\models\Article;
use app\models\ArticleHasAnchors;
use app\models\ArticleHasMedia;
use app\models\ArticleHasTags;
use app\models\ArticleHasTranslations;
use app\models\ArticleSearch;
use app\models\Language;
use app\models\Media;
use app\models\MediaHasTables;
use app\models\Tag;
use DOMDocument;
use Exception;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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

            if ($model->save()) {
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
     * @return Response
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
     * Sets articles as featured
     * @param $id
     * @return Response
     */
    public function actionSetFeatured($id)
    {
        $art = Article::findOne($id);
        $trans = $art->translations;
        $trans->featured = (int)!$trans->featured;
        $trans->save();
        Yii::$app->session->setFlash('success', $trans->featured ? Yii::t('app', 'article is now featured') :  Yii::t('app', 'article is no longer featured')) ;
        return $this->goBack();
    }

    /**
     * Saves all articles again to regenerate certain contents
     * @return Response
     */
    public function actionRegenerateArticles(){

        foreach(Article::find()->all() as $article){
            $article->save();
        }

        return $this->goBack();
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
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

}
