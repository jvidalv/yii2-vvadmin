<?php

namespace app\controllers;

use Yii;
use app\models\Noticia;
use app\models\NoticiaSearch;
use app\controllers\JosepController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NoticiaController implements the CRUD actions for Noticia model.
 */
class NoticiaController extends MainController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Noticia models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NoticiaSearch();

        $params = Yii::$app->request->queryParams;
        $dataProvider =  $searchModel->search($params);
        $dataProvider->pagination->pageSize=10;

        $tamanyActives = $searchModel->numeroTotal(['NoticiaSearch' => ['borrat' => 0]]);
        $tamanyPapelera = $searchModel->numeroTotal(['NoticiaSearch' => ['borrat' => 1]]);

        /* pestanya activa */
        $activa = 'totals';
        if(isset($params['NoticiaSearch']['borrat'])):
          $activa = $params['NoticiaSearch']['borrat'] ? 'paperera' : 'totals';
        endif;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tamanyActives' => $tamanyActives,
            'tamanyPapelera' => $tamanyPapelera,
            'activa' => $activa
        ]);
    }

    /**
     * Creates a new Noticia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Noticia();

        $model->user_id = Yii::$app->user->identity->id;

        $model->save(false);

        return $this->redirect(['update', 'id' => $model->id]);
    }

    /**
     * Updates an existing Noticia model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        /* controlem propietats temporals carregades en ajax */
        $temp = $model->imatge;

        if ($model->load(Yii::$app->request->post())) {
          $model->imatge = $temp;
          if($model->save()){
            if($model->data_publicacio) $model->data_publicacio_string = strftime("%a, %e de %B del %G a les %H:%M", strtotime($model->data_publicacio));
            Yii::$app->session->setFlash('general', "Notícia actualitzada correctament!");
          }
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Soft-Deletes an existing Noticia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $noticia = $this->findModel($id);
        $noticia->borrat = 1;
        $noticia->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Restores an existing Noticia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRestaurar($id)
    {
        $noticia = $this->findModel($id);
        $noticia->borrat = 0;
        $noticia->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Noticia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Noticia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Noticia::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
