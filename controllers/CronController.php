<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Models;
use ExponentPhpSDK;

//Controlem tot lo que pasa a un beforeaction, tots han dextendre deste menos lo SiteController
class CronController extends Controller
{
  /**
   * @inheritdoc
  */
  public function behaviors()
  {
      return [
          'access' => [
              'class' => AccessControl::className(),
              'rules' => [
                  [
                      'allow' => true,
                  ],
              ],
          ],
      ];
  }

}
