<?php


namespace app\modules\api\controllers;


use app\models\ArticleHasClaps;
use app\modules\api\models\Article;
use app\modules\api\models\ArticleHasTranslations;
use Yii;
use yii\db\ActiveRecord;

class ArticleController extends ApiController
{
    /**
     * @return Article|array|ActiveRecord|null
     */
    public function actionOne()
    {
        return Article::find()->alias('a')
            ->where(['slug' => Yii::$app->request->get('slug')])
            ->with('translations')->with('articleHasAnchors')->with('articleHasTags')->with('category')->with('articleHasSources')
            ->one();
    }

    /**
     * @return Article[]|ArticleHasTranslations[]|array|ActiveRecord[]
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
            return $obj->id;
        }, $articles)])->andWhere(['t.state' => Article::STATE_PUBLISHED])->all();

        if (!$related) {
            $related = ArticleHasTranslations::find()->where(['not', ['article_' . $aArticle->language_id => $aArticle->id]])->andWhere(['t.state' => Article::STATE_PUBLISHED])->all();
        }
        return $related;
    }

    /**
     * @return ArticleHasTranslations[]|array|ActiveRecord[]
     */
    public function actionAll()
    {
        return ArticleHasTranslations::find()->all();
    }

    /**
     * Adds one clap to the article, not before checking that it is not the same user twice!
     * @return bool
     */
    public function actionClap()
    {
        $article = Article::findOne(['slug' => Yii::$app->request->get('slug')]);
        if ($article && !ArticleHasClaps::findOne(['article_has_translations_id' => $article->translations->id, 'voter_ip' => $_SERVER['REMOTE_ADDR']])) {
            $article->translations->claps++;
            $article->translations->save();
            $clap = new ArticleHasClaps();
            $clap->setAttributes(
                [
                    'article_has_translations_id' => $article->translations->id,
                    'voter_ip' => $_SERVER['REMOTE_ADDR'],
                ]
            );
            $clap->save();
        }
        return true;
    }
}