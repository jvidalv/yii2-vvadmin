<?php

namespace app\controllers;

use app\models\Article;
use app\models\ArticleHasTags;
use app\models\ArticleSearch;
use app\models\Language;
use app\models\Tag;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends MainController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($model->tags) {

                $tags = explode(',', $model->tags);
                array_map(function (ArticleHasTags $val) {
                    $val->delete();
                }, $model->articleHasTags);

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

            Yii::$app->session->setFlash('success', Yii::t('app', 'saved!'));
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
            $newArticle->title = "($lang->code) $newArticle->title";
            $newArticle->created_at = null;
            $newArticle->updated_at = null;
            $newArticle->language_id = $lang->code;
            $newArticle->translating = 1;

            if ($newArticle->save()) {
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

}