<?php

namespace app\controllers;

use app\models\Article;
use app\models\Media;
use app\models\MediaSearch;
use app\models\User;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\imagine\Image;
use yii\web\BadRequestHttpException;
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
     * @param $table
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUploadFiles($id = null, $table)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $files = UploadedFile::getInstancesByName('media_upload');

        foreach ($files as $file) {
            $media = new Media();
            $media->table = $table;
            $media->table_id = $id;
            $media->user_id = Yii::$app->user->identity->id;
            $media->file = [$file];

            if ($media->validate()) {

                if (!is_dir('uploads/' . date("Y") . '/')) mkdir('uploads/' . date("Y"), 0755); // carpeta any
                if (!is_dir('uploads/' . date("Y") . '/' . date("m") . '/')) mkdir('uploads/' . date("Y") . '/' . date("m"), 0755); // carpeta mes
                if (!is_dir('uploads/' . date("Y") . '/' . date("m") . '/' . $table . '/')) mkdir('uploads/' . date("Y") . '/' . date("m") . '/' . $table, 0755); // carpeta tipo

                $path = 'uploads/' . date("Y") . '/' . date("m") . '/' . $table . '/';
                // save in false to get an ID
                $media->save(false);
                $full_path = $path . '' . $table . '-' . $media->id . '.' . $file->extension;

                // comprobem si es posible guardar sino borrem
                if ($file->saveAs($full_path)) {
                    $media->path = $path;
                    $media->titol = addslashes($file->basename);
                    $media->file_name = $table . '-' . $media->id . '.' . $file->extension;
                    $media->save(false);
                    if ($id) $media->guardarObjecte($id, $table);

                    // comprobem is es imatge i redimensionem
                    if ($sizes = getimagesize($full_path)) {
                        $media->es_imatge = 1;
                        $media->save(false);
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
     * @param $table
     * @return bool
     */
    public function actionDeleteFiles($id, $table)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Media::esborrarMedia($id, $table);
    }

    /**
     * Returns an image
     * @param $table
     * @param $table_id
     * @param array $size
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionGetImage($table, $table_id, $size = [])
    {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;
        $size = $size ? json_decode($size) : [];
        /**
         * If we want an image that its origin is on media, we can't query by parameters other than primary key
         */
        if ($table === Media::TBL_MEDIA) {
            $media = Media::findOne($table_id);
        } else {
            $media = Media::find()->where(['table' => $table, 'table_id' => $table_id])->orderBy('id desc')->one();
        }

        /**
         * 1. Check if media exist at all
         * 2. Check if file that media points exist
         * 3. Check if file of media with the sizes exist, if not create it and return the path
         * 4. Return the image
         */
        if (!$media) {
            $response->stream = $this->imageFallback($table);
        // Si existe media y no existe la imagen devolvemos un 404 con el tamaño especificado
        } else if (!file_exists($media->getFullPath())) {

            if (!file_exists(Media::PATH_TO_TEMPORARY . $size[0] . $size[1] . '404.jpg')) {
                if (!$path = $this->generate404Image($size)) {
                    $response->stream = fopen(Media::PATH_TO_TEMPORARY . '404.jpg', 'r');
                } else {
                    $response->stream = fopen(Media::PATH_TO_TEMPORARY . $size[0] . $size[1] . '404.jpg', 'r');
                }
            } else {
                $response->stream = fopen(Media::PATH_TO_TEMPORARY . $size[0] . $size[1] . '404.jpg', 'r');
            }

        } else if (!file_exists($media->getFullPath($size))) {

            if (!$path = $this->generateImage($media, $size)) {
                $response->stream = $this->imageFallback($table);
            } else {
                $response->stream = fopen($media->getFullPath($size), 'r');
            }

        } else {
            $response->stream = fopen($media->getFullPath($size), 'r');
        }

        if (!is_resource($response->stream)) {
            throw new \yii\web\ServerErrorHttpException('file access failed: permission deny');
        }

        return $response->send();
    }


    /**
     * @param Media $media
     * @param $size
     * @return bool|\Imagine\Image\ImageInterface
     */
    private function generateImage(Media $media, $size)
    {
        try {
            $image = Image::thumbnail($media->getFullPath(), $size[0], $size[1])
                ->save($media->path . $size[0] . '-' . $size[1] . '-' . $media->file_name, ['quality' => 100]);

            return $image;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $size
     * @return bool|\Imagine\Image\ImageInterface
     */
    private function generate404Image($size)
    {
        try {
            $image = Image::thumbnail(Media::PATH_TO_DEFAULTS . '404.jpg', $size[0], $size[1])
                ->save(Media::PATH_TO_TEMPORARY . $size[0] . $size[1] . '404.jpg', ['quality' => 100]);
            return $image;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $table
     * @return bool
     */
    private
    function imageFallback($table = Media::TBL_MEDIA)
    {
        switch ($table) {
            case Media::TBL_ARTICLE:
                return fopen('images/defaults/65-article.png', 'r');
            case Media::TBL_USER:
                return fopen('images/defaults/user.png', 'r');
            default:
                return fopen('images/defaults/404.jpg', 'r');
        }
    }

    /**
     * @param $code
     * @throws \yii\web\ServerErrorHttpException
     */
    public
    function actionGetLanguageImage($code)
    {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;
        if (!is_resource($response->stream = fopen('images/lang/' . $code . '.png', 'r'))) {
            throw new \yii\web\ServerErrorHttpException('file access failed: permission deny');
        }
        return $response->send();
    }

}
