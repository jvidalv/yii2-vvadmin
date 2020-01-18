<?php


namespace app\modules\api\controllers;


use app\modules\api\models\Article;
use app\modules\api\models\ArticleHasTranslations;
use Yii;

class ArticleController extends ApiController
{
    /**
     * @return Article|array|\yii\db\ActiveRecord|null
     */
    public function actionOne()
    {
        return Article::find()->alias('a')
            ->where(['slug' => Yii::$app->request->get('slug')])
            ->with('translations')->with('articleHasAnchors')->with('articleHasTags')->with('category')->with('articleHasSources')
            ->one();
    }

    /**
     * @return Article[]|ArticleHasTranslations[]|array|\yii\db\ActiveRecord[]
     */
    public function actionRelated()
    {
        $aArticle = Article::findOne(['slug' => Yii::$app->request->get('slug')]);
        $continuation = $aArticle->continuationA ? $aArticle->continuationA->continuation_id : null;
        $articles = Article::find()->alias('a')
            ->where(['language_id' => $aArticle->language_id])
            ->andWhere(['!=', 'id', $aArticle->id])
            ->andWhere(['or', ['id' => $continuation], ['category_id' => $aArticle->category_id]])
            ->with('translations')->with('articleHasAnchors')->with('articleHasTags')->with('category')
            ->orderBy(['date' => 'DESC'])
            ->limit(Yii::$app->request->get('limit'))
            ->all();
        $related = ArticleHasTranslations::find()->where(['in', 'article_' . $aArticle->language_id, array_map(function ($obj) {
            return $obj->id;}, $articles)])->all();

        if(!$related) {
            $related = ArticleHasTranslations::find()->where(['not', ['article_' . $aArticle->language_id => $aArticle->id]])->all();
        }
        return $related;
    }

    /**
     * @return ArticleHasTranslations[]|array|\yii\db\ActiveRecord[]
     */
    public function actionAll()
    {
        return ArticleHasTranslations::find()->all();
    }
}