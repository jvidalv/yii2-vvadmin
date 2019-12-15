<?php


namespace app\modules\api\controllers;


use app\modules\api\models\Article;
use app\modules\api\models\ArticleHasTranslations;
use Faker\Factory;
use Yii;
use yii\rest\ActiveController;
use yii\rest\Controller;

class ArticleController extends ApiController
{
    /**
     * @return Article|array|\yii\db\ActiveRecord|null
     */
    public function actionOne(){
        return Article::find()->alias('a')
            ->where(['slug' => Yii::$app->request->get('slug')])
            ->with('translations')->with('articleHasAnchors')->with('articleHasTags')->with('category')
            ->one();
    }

    /**
     * @return Article[]|ArticleHasTranslations[]|array|\yii\db\ActiveRecord[]
     */
    public function actionRelated()
    {
        return Article::find()->alias('a')
            ->where(['!=', 'id', Yii::$app->request->get('id')])
            ->andWhere(['category_id' => Yii::$app->request->get('category_id')])
            ->with('translations')->with('articleHasAnchors')->with('articleHasTags')->with('category')
            ->orderBy(['date' => 'DESC'])
            ->limit(Yii::$app->request->get('limit'))
            ->all();
    }

    /**
     * @return ArticleHasTranslations[]|array|\yii\db\ActiveRecord[]
     */
    public function actionAll(){
        return ArticleHasTranslations::find()->all();
    }
}