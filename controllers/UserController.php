<?php

namespace app\controllers;

use app\models\Language;
use app\models\User;
use app\models\UserSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends MainController
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->isAdmin()) {
            // carreguem modal i dades per al modal de creacio
            $model = new User();
            $model->password = bin2hex(openssl_random_pseudo_bytes(3));

            if ($model->load(Yii::$app->request->post())) {
                $model->poble_id = Yii::$app->user->identity->isAdmin() ? $model->poble_id : Yii::$app->user->identity->poble_id;
                $model->setPassword($model->password);
                $model->password = $model->password_hash;
                if ($model->save()) {
                    Yii::$app->session->setFlash('general', "Usuari afegit correctament!");
                    return $this->redirect(['update', 'slug' => $model->slug, 'id' => $model->id]);
                }
            }

            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->goHome();
    }

    /**
     * @param bool $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpdate($id = false)
    {
        $model = $this->findModel($id ? $id : Yii::$app->user->identity->id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->password && $model->password != '******') {
                $model->setPassword($model->password);
                $model->password = $model->password_hash;
            }
            $model->save();
            Yii::$app->session->setFlash('general', Yii::t('app', 'Data saved correctly'));
        }

        $model->password = '******';

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Changes the user language
     */
    public function actionChangeLanguage()
    {
        $lang = Yii::$app->request->post()['lang'];

        $session = Yii::$app->session;
        $session->set('language', $lang);

        $query = Language::findOne(['code' => $lang]);

        if ($query) {
            Yii::$app->user->identity->language_id = $query->code;
            Yii::$app->user->identity->save();
        }

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

}
