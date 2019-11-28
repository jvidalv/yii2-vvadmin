<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Article;
use Faker\Factory;
use yii\console\Controller;

/**
 * Class FakerController
 * @package app\commands
 */
class FakerController extends Controller
{
    public function actionGenerateArticles($number = 0)
    {
        $i = 0;
        $faker = Factory::create();
        while($i < $number){
            $art = new Article();
            $art->setAttributes([
                'language_id' => 'ca',
                'user_id' => 1,
                'category_id' => 1,
                'date' => '12-12-2019 12:25',
                'title' => $faker->text(120),
                'resume' => $faker->text(180),
                'content' => $faker->randomHtml(),
                'state' => 1,
            ]);
            if($art->save()){
                $artEN = new Article();
                $artEN->setAttributes([
                    'translation_of' => $art->id,
                    'language_id' => 'en',
                    'user_id' => 1,
                    'category_id' => 1,
                    'date' => '12-12-2019 12:25',
                    'title' => $faker->text(120),
                    'resume' => $faker->text(180),
                    'content' => $faker->randomHtml(),
                    'state' => 1,
                ]);
                $artEN->save();

                $artES = new Article();
                $artES->setAttributes([
                    'translation_of' => $art->id,
                    'language_id' => 'es',
                    'user_id' => 1,
                    'category_id' => 1,
                    'date' => '12-12-2019 12:25',
                    'title' => $faker->text(120),
                    'resume' => $faker->text(180),
                    'content' => $faker->randomHtml(),
                    'state' => 1,
                ]);
                $artES->save();
            }
            $i++;
        }
    }
}
