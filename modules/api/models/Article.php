<?php

namespace app\modules\api\models;

class Article extends \app\models\Article
{
    public $trans_id;
    public $slug_ca, $slug_es, $slug_en;
    public $time_to_read;

    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
                'time_to_read' => function () {
                    return $this->translations->time_to_read;
                },
                'date_nice' => function () {
                    setlocale(LC_TIME, [$this->language_id]);
                    return strftime("%b%e, %G");
                },
                'tags' => function () {
                    return $this->articleHasTags;
                },
                'anchors' => function () {
                    return $this->articleHasAnchors;
                },
                'translations' => function () {
                    return [
                        'trans_id' => $this->translations->id,
                        'slug_ca' => $this->translations->articleCa->slug,
                        'slug_es' => $this->translations->articleEs->slug,
                        'slug_en' => $this->translations->articleEn->slug,
                    ];
                },
            ]
        );
    }

}