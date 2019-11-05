<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\LoginForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

class SiteController extends TopController
{

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
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'upload-imatge', 'upload-files', 'delete-imatge', 'set-session'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
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
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $data = \Yii::$app->request->post()['data'];
            $user = User::find()->where(['or', ['email' => $data]])->one();
            if ($user) {
                return ['status' => true, 'nom' => $user->fullName, 'imatge' => $user->media ? $user->media->getUrlImatge() : ''];
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
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            $model = new LoginForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->redirect(['/']);
            }
        }
        return ['status' => false, 'error' => Yii::t('app', 'La contrassenya no és correcta')];
    }

}
