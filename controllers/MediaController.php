<?php

namespace app\controllers;

use Yii;
use app\models\Media;
use app\models\MediaSearch;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\imagine\Image;
use yii\web\Controller;
use yii\web\Response;
/**
 * MediaController implements the CRUD actions for Media model.
 */
class MediaController extends MainController
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
     * Lists all Media models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search($params);

        /* pestanya activa */
        $activa = 'totals';
        if(isset($params['MediaSearch']['es_imatge'])):
          $activa = (int)$params['MediaSearch']['es_imatge'] > 0 ? 'imatges' : ($params['MediaSearch']['es_imatge'] == 'null' ? 'totals' : 'documents');
        endif;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'activa' => $activa,
        ]);
    }

    /**
     * Updates an existing Media model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderPartial('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Media model using ajax.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateAjax($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post());
        $model->descripcio = $model->descripcio == '' ? null : $model->descripcio;
        return $model->save(false);
    }

    /**
     * Displays a single Media model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Media model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Media();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }



    /**
     * Deletes an existing Media model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $base_dir = realpath($_SERVER["DOCUMENT_ROOT"]);
        if(file_exists($base_dir . $model->getUrl())) unlink($base_dir . $model->getUrlImatge());
        if(file_exists($base_dir . $model->getUrlImatge('miniatura'))) unlink($base_dir . $model->getUrlImatge('miniatura'));
        if(file_exists($base_dir . $model->getUrlImatge(65)))  unlink($base_dir . $model->getUrlImatge(65));
        if(file_exists($base_dir . $model->getUrlImatge(150)))  unlink($base_dir . $model->getUrlImatge(150));
        if(file_exists($base_dir . $model->getUrlImatge(250)))  unlink($base_dir . $model->getUrlImatge(250));
        if(file_exists($base_dir . $model->getUrlImatge(750)))  unlink($base_dir . $model->getUrlImatge(750));
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Media model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Pujar media
     * @return json
     */
     public function actionUploadFiles($id = false, $tipo)
     {
       \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $files = UploadedFile::getInstancesByName('media_upload');
      foreach($files as $file){
        $media = new Media();
        $media->tipo = $tipo;
        $media->user_id = Yii::$app->user->identity->id;
        $media->file = [$file];
        // validem lo fitxer
        if($media->validate()){
            // comprobem paths
            if(!is_dir('uploads/' . date("Y") . '/')) mkdir('uploads/' . date("Y"), 0755); // carpeta any
            if(!is_dir('uploads/' . date("Y") . '/' . date("m") . '/')) mkdir('uploads/' . date("Y") . '/' . date("m"), 0755); // carpeta mes
            if(!is_dir('uploads/' . date("Y") . '/' . date("m") . '/' . $tipo . '/')) mkdir('uploads/' . date("Y") . '/' . date("m") . '/' .$tipo, 0755); // carpeta tipo
            $path = 'uploads/' . date("Y") . '/' . date("m") . '/' . $tipo . '/';
            // guardem en fals per a generar id
            $media->save(false);
            $full_path = $path . '' . $tipo . '-' . $media->id . '.' . $file->extension;
            // comprobem si es posible guardar sino borrem
            if($file->saveAs($full_path)){
              $media->path = '/' . $path;
              $media->titol = addslashes($file->basename);
              $media->file_name = $tipo . '-' . $media->id . '.' . $file->extension;
              $media->save(false);
              if($id) $media->guardarObjecte($id, $tipo);
              // comprobem is es imatge i redimensionem
              if($sizes = getimagesize($full_path)){
                $media->es_imatge = 1;
                $media->save(false);
                /* thumb 65x65 */
                Image::thumbnail($full_path, $sizes[0]/2, $sizes[1]/2)
                ->save($path . $media::MINIATURA . $tipo . '-' . $media->id . '.' . $file->extension,['quality' => 70]);
                /* thumb 65x65 */
                Image::thumbnail($full_path, 65, 65)
                ->save($path . $media::THUMB65 . $tipo . '-' . $media->id . '.' . $file->extension,['quality' => 70]);
                /* thumb 250x250 */
                Image::thumbnail($full_path, 250, 250)
                ->save($path . $media::THUMB250 . $tipo . '-' . $media->id . '.' . $file->extension,['quality' => 70]);
              }
            } else {
              $media->delete();
            }
          }
        }
      return true;
    }

    // Descarreguem
    public function actionDescarregar($id)
    {
      $model = $this->findModel($id);
      $base_dir = realpath($_SERVER["DOCUMENT_ROOT"]);
      if (file_exists($base_dir . $model->getUrl())){
        Yii::$app->response->sendFile($base_dir . $model->getUrl());
        return true;
      }
      return $this->redirect(['index']);
    }

    /**
    * Esborrar media relacio by @Josep Vidal
    * @return json
    * Espere una ID, un tipo de OBJECTE
    */
    public function actionDeleteFiles($id, $tipo)
    {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return Media::esborrarMedia($id, $tipo);
    }
}
