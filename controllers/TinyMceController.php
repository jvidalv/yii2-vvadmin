<?php


namespace app\controllers;

use app\models\Media;
use Yii;
use yii\imagine\Image;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class TinyMceController extends \yii\base\Controller
{

    public function actionUploadImageFromTiny()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $getParams = Yii::$app->request->get();
        $postParams = Yii::$app->request->post();

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
        return ['location' => 'eioooooooo'];
    }

}