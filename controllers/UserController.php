<?php

namespace app\controllers;

use Yii;
use app\controllers\JosepController;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends MainController
{

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
      if(Yii::$app->user->identity->esAdmin()){
        // carreguem modal i dades per al modal de creacio
        $model = new User();
        $model->password = bin2hex(openssl_random_pseudo_bytes(3));

        if($model->load(Yii::$app->request->post())) {
            $model->poble_id = Yii::$app->user->identity->esAdmin() ? $model->poble_id : Yii::$app->user->identity->poble_id;
            $model->setPassword($model->password);
            $model->password = $model->password_hash;
            if($model->save()){
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
      } else {
        return $this->goHome();
      }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = false)
    {
        $model = $this->findModel($id ? $id : Yii::$app->user->identity->id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->password && $model->password != '******') {
              $model->setPassword($model->password);
              $model->password = $model->password_hash;
            }
            $model->save();
            Yii::$app->session->setFlash('general', "Dades actualitzades correctament!");
        }

        $model->password = '******';

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();
    //
    //     return $this->redirect(['index']);
    // }

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
}
