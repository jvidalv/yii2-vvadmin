<?php

namespace app\modules\api\models;

class Article extends \app\models\Article
{
    public $category_nice, $category_code;
    public $trans_id;
    public $ca, $es, $en;
    public $time_to_read;

    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
                'image' => function () {
                    return Media::img($this->id, Media::TBL_ARTICLE, [200, 200]);
                },
                'category_nice' => function () {
                    return $this->category["name_$this->language_id"];
                },
                'category_code' => function () {
                    return $this->category->code;
                },
                'time_to_read' => function () {
                    return $this->translations->time_to_read;
                },
                'claps' => function () {
                    return $this->translations->claps;
                },
                'date_nice' => function () {
                    setlocale(LC_TIME, $this->getLocale());
                    return strftime("%b %e, %G", strtotime($this->date));
                },
                'tags' => function () {
                    return $this->articleHasTags;
                },
                'anchors' => function () {
                    return $this->articleHasAnchors;
                },
                'sources' => function () {
                    return $this->articleHasSources ? ArticleHasSources::mapSourcesToLabels($this->articleHasSources) : null;
                },
                'translations' => function () {
                    return [
                        'id' => $this->translations->id,
                        'ca' => $this->translations->articleCa->slug,
                        'es' => $this->translations->articleEs->slug,
                        'en' => $this->translations->articleEn->slug,
                    ];
                },
            ]
        );
    }

}