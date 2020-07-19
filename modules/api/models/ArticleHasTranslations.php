<?php


namespace app\modules\api\models;


use yii\db\ActiveQuery;

class ArticleHasTranslations extends \app\models\ArticleHasTranslations
{
    public $title_ca, $title_es, $title_en;
    public $resume_ca, $resume_es, $resume_en;
    public $slug_ca, $slug_es, $slug_en;

    /**
     * {@inheritdoc}
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return parent::find()->alias('t')->with('category')
            ->where(['t.state' => Article::STATE_PUBLISHED])
            ->leftJoin('article as ca', 'ca.id = t.article_ca')
            ->leftJoin('article as es', 'es.id = t.article_es')
            ->leftJoin('article as en', 'en.id = t.article_en')
            ->select(['*',
                'ca.title as title_ca', 'ca.resume as resume_ca', 'ca.slug as slug_ca',
                'es.title as title_es', 'es.resume as resume_es', 'es.slug as slug_es',
                'en.title as title_en', 'en.resume as resume_en', 'en.slug as slug_en',
            ])->orderBy('t.date DESC');
    }

    public function fields()
    {
        $category = $this->category;
        return [
            'id',
            'ca' => function ($model) use ($category) {
                setlocale(LC_TIME, array('catalan'));
                return [
                    'id' => $model->article_ca,
                    'title' => $model->title_ca,
                    'resume' => $model->resume_ca,
                    'slug' => $model->slug_ca,
                    'category' => $category->name_ca,
                    'date' => utf8_encode(strftime("%b%e, %G")),
                    'image' => Media::img($model->article_ca, Media::TBL_ARTICLE, [200, 200])
                ];
            },
            'es' => function ($model) use ($category) {
                setlocale(LC_TIME, array('spanish'));
                return [
                    'id' => $model->article_es,
                    'title' => $model->title_es,
                    'resume' => $model->resume_es,
                    'slug' => $model->slug_es,
                    'category' => $category->name_es,
                    'date' => utf8_encode(strftime("%b%e, %G")),
                    'image' => Media::img($model->article_es, Media::TBL_ARTICLE, [200, 200])
                ];
            },
            'en' => function ($model) use ($category) {
                setlocale(LC_TIME, array('english'));
                return [
                    'id' => $model->article_en,
                    'title' => $model->title_en,
                    'resume' => $model->resume_en,
                    'slug' => $model->slug_en,
                    'category' => $category->name_en,
                    'date' => utf8_encode(strftime("%b%e, %G")),
                    'image' => Media::img($model->article_en, Media::TBL_ARTICLE, [200, 200])
                ];
            },
            'featured',
            'date',
            'category' => function ($model) use ($category) {
                return $category->code;
            }
        ];
    }
}