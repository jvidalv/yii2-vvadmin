<?php

namespace app\modules\api\models;

class Article extends \app\models\Article
{
    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
                'anchors' => function () {
                    return $this->articleHasAnchors;
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