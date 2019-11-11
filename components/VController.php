<?php


namespace app\components;

use app\models\Language;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class VController
 * @package app\components
 */
class VController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->language = $this->getLanguage();
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'existeix-usuari', 'contrassenya-correcta'],
                        'roles' => ['?'],
                        'allow' => true,
                    ],
                    [
                        'roles' => ['@'],
                        'allow' => true,
                        'denyCallback' => function($action){
                            return $action->controller->redirect(['site/login']);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'delete' => ['post']
                ],
            ],
        ];
    }

    /**
     * We set the session language based on user or if guest, in the explorer
     * @return false|string
     */
    private function getLanguage()
    {
        if(!Yii::$app->user->isGuest) {
            return Yii::$app->user->identity->language_id;
        } else {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $langFound = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                if (in_array($langFound, ['ca', 'es', 'en'])) {
                    return $langFound;
                }
            }
            return Language::LANG_EN;
        }
    }
}