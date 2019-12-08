<?php

namespace app\controllers;

use app\components\VController;
use app\models\Media;
use app\models\MediaHasTables;
use app\models\MediaSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * MediaController implements the CRUD actions for Media model.
 */
class MediaController extends VController
{
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
     * @param null $table_id
     * @param null $table_name
     * @return bool
     * @throws \yii\db\Exception
     */
    public function actionUploadFiles($table_id = null, $table_name = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $files = UploadedFile::getInstancesByName('media_upload');
        $con = Yii::$app->db->beginTransaction();

        foreach ($files as $file) {

            $media = new Media();
            $media->user_id = Yii::$app->user->identity->id;
            $media->file = [$file];

            if ($media->validate()) {

                Media::generateFoldersByTableName($table_name);

                $path = 'uploads/' . date("Y") . '/' . date("m") . '/' . $table_name . '/';
                // save in false to get an ID
                $media->save(false);
                if ($table_id && $table_name && $media->id) {
                    $mediaRelation = new MediaHasTables();
                    $mediaRelation->setAttributes([
                        'table_name' => $table_name,
                        'table_id' => $table_id,
                        'media_id' => $media->id
                    ]);
                    $mediaRelation->save();
                }
                $full_path = $path . '' . $table_name . '-' . $media->id . '.' . $file->extension;
                // comprobem si es posible guardar sino borrem
                if ($file->saveAs($full_path)) {
                    $media->path = $path;
                    $media->titol = addslashes($file->basename);
                    $media->file_name = $table_name . '-' . $media->id . '.' . $file->extension;
                    $media->save(false);
                    // check if is image
                    if ($sizes = getimagesize($full_path)) {
                        $media->es_imatge = 1;
                        $media->save(false);
                    }
                    $con->commit();
                } else {
                    $con->rollBack();
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
     * @param string $size line
     * @throws ServerErrorHttpException
     */
    public function actionGetImage($table, $table_id, $size)
    {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;
        $size = Media::limit_size(json_decode($size));

        /**
         * If we want an image that its origin is on media, we can't query by parameters other than primary key
         */
        $media = Media::find()->alias('m')
            ->leftJoin('media_has_tables as mt', 'mt.media_id = m.id')
            ->where(['mt.table_name' => $table, 'mt.table_id' => $table_id])->orderBy('id desc')->one();


        if ($media) {
            if (file_exists($media->getFullPath($size))) {
                $response->stream = fopen($media->getFullPath($size), 'r');
            } else if (file_exists($media->getFullPath())) {
                $response->stream = fopen(Media::generate_image($media->file_name, $media->path, $size), 'r');
            }
        }

        if (!is_resource($response->stream)) {
            switch ($table) {
                case Media::TBL_USER:
                    $response->stream = fopen(Media::generate_image('user.png', Media::PATH_TO_DEFAULTS, $size, Media::PATH_TO_TEMPORARY), 'r');
                    break;
                case Media::TBL_ARTICLE:
                    $response->stream = fopen(Media::generate_image('article.png', Media::PATH_TO_DEFAULTS, $size, Media::PATH_TO_TEMPORARY), 'r');
                    break;
                default:
                    $response->stream = fopen(Media::generate_image('404.jpg', Media::PATH_TO_DEFAULTS, $size, Media::PATH_TO_TEMPORARY), 'r');
                    break;
            }
        }

        if (!is_resource($response->stream)) {
            throw new ServerErrorHttpException('something when wrong when generating the images');
        }

        return $response->send();
    }

    /**
     * @param $code
     * @throws ServerErrorHttpException
     */
    public
    function actionGetLanguageImage($code)
    {
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->format = Response::FORMAT_RAW;
        if (!is_resource($response->stream = fopen('images/lang/' . $code . '.png', 'r'))) {
            throw new ServerErrorHttpException('file access failed: permission deny');
        }
        return $response->send();
    }

}
