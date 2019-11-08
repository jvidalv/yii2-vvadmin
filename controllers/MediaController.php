<?php

namespace app\controllers;

use app\models\Article;
use app\models\Media;
use app\models\MediaSearch;
use app\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\imagine\Image;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

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
        if (isset($params['MediaSearch']['es_imatge'])):
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
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $base_dir = realpath($_SERVER["DOCUMENT_ROOT"]);
        if (file_exists($base_dir . $model->getUrl())) unlink($base_dir . $model->getUrlImatge());
        if (file_exists($base_dir . $model->getUrlImatge('miniatura'))) unlink($base_dir . $model->getUrlImatge('miniatura'));
        if (file_exists($base_dir . $model->getUrlImatge(65))) unlink($base_dir . $model->getUrlImatge(65));
        if (file_exists($base_dir . $model->getUrlImatge(150))) unlink($base_dir . $model->getUrlImatge(150));
        if (file_exists($base_dir . $model->getUrlImatge(250))) unlink($base_dir . $model->getUrlImatge(250));
        if (file_exists($base_dir . $model->getUrlImatge(750))) unlink($base_dir . $model->getUrlImatge(750));
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * @param bool $id
     * @param $tipo
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUploadFiles($id = false, $tipo)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $files = UploadedFile::getInstancesByName('media_upload');
        foreach ($files as $file) {
            $media = new Media();
            $media->tipo = $tipo;
            $media->user_id = Yii::$app->user->identity->id;
            $media->file = [$file];
            // validem lo fitxer
            if ($media->validate()) {
                // comprobem paths
                if (!is_dir('uploads/' . date("Y") . '/')) mkdir('uploads/' . date("Y"), 0755); // carpeta any
                if (!is_dir('uploads/' . date("Y") . '/' . date("m") . '/')) mkdir('uploads/' . date("Y") . '/' . date("m"), 0755); // carpeta mes
                if (!is_dir('uploads/' . date("Y") . '/' . date("m") . '/' . $tipo . '/')) mkdir('uploads/' . date("Y") . '/' . date("m") . '/' . $tipo, 0755); // carpeta tipo
                $path = 'uploads/' . date("Y") . '/' . date("m") . '/' . $tipo . '/';
                // guardem en fals per a generar id
                $media->save(false);
                $full_path = $path . '' . $tipo . '-' . $media->id . '.' . $file->extension;
                // comprobem si es posible guardar sino borrem
                if ($file->saveAs($full_path)) {
                    $media->path = $path;
                    $media->titol = addslashes($file->basename);
                    $media->file_name = $tipo . '-' . $media->id . '.' . $file->extension;
                    $media->save(false);
                    if ($id) $media->guardarObjecte($id, $tipo);
                    // comprobem is es imatge i redimensionem
                    if ($sizes = getimagesize($full_path)) {
                        $media->es_imatge = 1;
                        $media->save(false);
                        /* cut in half */
                        Image::thumbnail($full_path, $sizes[0] / 2, $sizes[1] / 2)
                            ->save($path . $media::MINIATURA . $tipo . '-' . $media->id . '.' . $file->extension, ['quality' => 70]);
                        /* thumb 65x65 */
                        Image::thumbnail($full_path, 65, 65)
                            ->save($path . $media::THUMB65 . $tipo . '-' . $media->id . '.' . $file->extension, ['quality' => 70]);
                        /* thumb 250x250 */
                        Image::thumbnail($full_path, 250, 250)
                            ->save($path . $media::THUMB250 . $tipo . '-' . $media->id . '.' . $file->extension, ['quality' => 70]);
                        Image::thumbnail($full_path, 500, 500)
                            ->save($path . $media::THUMB500 . $tipo . '-' . $media->id . '.' . $file->extension, ['quality' => 70]);
                    }
                } else {
                    $media->delete();
                }
            }
        }
        return true;
    }

    /**
     * @param $id
     * @return bool|Response
     * @throws NotFoundHttpException
     */
    public function actionDescarregar($id)
    {
        $model = $this->findModel($id);
        $base_dir = realpath($_SERVER["DOCUMENT_ROOT"]);
        if (file_exists($base_dir . $model->getUrl())) {
            Yii::$app->response->sendFile($base_dir . $model->getUrl());
            return true;
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @param $tipo
     * @return bool
     */
    public function actionDeleteFiles($id, $tipo)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Media::esborrarMedia($id, $tipo);
    }

    /**
     * @param $code
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionGetUserImage($id)
    {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;
        try {
            $user = User::findOne($id);
            is_resource($response->stream = fopen($user->media->getUrlImatge(), 'r'));
        } catch (\Exception $e) {
            is_resource($response->stream = fopen('images/defaults/user.png', 'r'));
        }

        return $response->send();
    }

    /**
     * @param $code
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionGetLanguageImage($code)
    {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;
        if (!is_resource($response->stream = fopen('images/lang/' . $code . '.png', 'r'))) {
            throw new \yii\web\ServerErrorHttpException('file access failed: permission deny');
        }
        return $response->send();
    }

    /**
     * @param $code
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionGetArticleImage($id, $size = Media::THUMB65)
    {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;
        $article = Article::findOne($id);
        if($article->media_id && $article->media){
            try{
                is_resource($response->stream = fopen($article->media->getUrlImatge($size), 'r'));
            } catch(\Exception $e){
                is_resource($response->stream = fopen('images/defaults/65-article.png', 'r'));
            }
        } else {
            is_resource($response->stream = fopen('images/defaults/65-article.png', 'r'));
        }

        return $response->send();
    }
}
