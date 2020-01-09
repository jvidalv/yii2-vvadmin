<?php

namespace app\controllers;

use app\components\VController;
use app\models\LoginForm;
use app\models\Media;
use app\models\User;
use Yii;
use yii\web\Response;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends VController
{

    /**
     * Display homepage.
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionError()
    {
            return $this->render('error');
    }

    /**
     * Login action.
     * @return Response|string
     */
    public function actionLogin()
    {
        // Inserts random home images to the background of the login page
        $randomBackground = ['/images/backgrounds/fondo-olivers-forques.jpg', '/images/backgrounds/fondo-flors.jpg', '/images/backgrounds/fondo-gebra.jpg',  '/images/backgrounds/fondo-mao-morro.jpg',  ];

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/']);
        }

        $model->password = '';
        return $this->renderPartial('login', [
            'model' => $model,
            'backgroundImageUrl' => $randomBackground[rand(0,3)],
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Ajax call to check if user exist
     * @return array
     */
    public function actionExisteixUsuari()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post()['data'];
            $user = User::find()->where(['or', ['email' => $data]])->one();
            if ($user) {
                return ['status' => true, 'nom' => $user->fullName, 'imatge' => Media::img($user->id, Media::TBL_USER, [117, 117])];
            }
        }

        return ['status' => false, 'error' => Yii::t('app', 'L\'usuari@ no existeix.')];
    }

    /**
     * Es la contrassenya correcta?
     * @return array|Response
     */
    public function actionContrassenyaCorrecta()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new LoginForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->redirect(['/']);
            }
        }
        return ['status' => false, 'error' => Yii::t('app', 'La contrassenya no és correcta')];
    }

}
