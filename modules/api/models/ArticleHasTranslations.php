<?php


namespace app\modules\api\models;


use yii\db\ActiveQuery;

class ArticleHasTranslations extends \app\models\ArticleHasTranslations
{
    public $title_ca, $title_es, $title_en;
    public $resume_ca, $resume_es, $resume_en;
    public $slug_ca, $slug_es, $slug_en;
    public $category_ca, $category_es, $category_en, $category_code;

    public function fields()
    {
        switch ($this->scenario) {
            default:
            case 'snippets':
                return [
                    'id',
                    'title_ca',
                    'title_es',
                    'title_en',
                    'resume_ca',
                    'resume_es',
                    'resume_en',
                    'category_code',
                    'category_ca',
                    'category_es',
                    'category_en',
                    'slug_ca',
                    'slug_es',
                    'slug_en',
                ];
        }
    }

    /**
     * {@inheritdoc}
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return parent::find()->alias('t')
            ->leftJoin('article as ca', 'ca.id = t.article_ca')
            ->leftJoin('article as es', 'es.id = t.article_es')
            ->leftJoin('article as en', 'en.id = t.article_en')
            ->leftJoin('category as ct', 'ct.id = t.category_id')
            ->select(['*',
                'ca.title as title_ca', 'ca.resume as resume_ca', 'ct.name_ca as category_ca', 'ca.slug as slug_ca',
                'es.title as title_es', 'es.resume as resume_es', 'ct.name_es as category_es', 'es.slug as slug_es',
                'en.title as title_en', 'en.resume as resume_en', 'ct.name_en as category_en', 'en.slug as slug_en',
                'ct.code as category_code',
            ]);
    }
}