<?php

namespace app\libraries;

use yii\imagine\Image;

/**
 * @author Josep Vidal
 */
class JosepImageFilter
{
    /* SIZES tipo = [amplada, altura] */
    const THUMBNAIL = [150, 150];
    const MEDIUM = [250, 250];
    const LARGE = [500, 500];

    /* QUALITY calitat 0-100 */
    const BAIXA = 50;
    const MITJA = 75;
    const ALTA = 100;

    /* TIPO nom  */
    CONST NOTICIA = 'noticies';
    CONST GENERAL = 'general';


    /* CONSTRUCTOR
    * req obj $file
    * req string $tipo const tipo
    * req int $id
    * int $qualitat
    * array [width, height] $size
    */
    function __construct($file, $tipo, $id, $quality = 75, $size = [])
    {
        $this->imatge = $file;
        $this->tipo = $tipo;
        $this->index = $this->getIndex();
        $this->quality = $quality > 100 ? 100 : ($quality < 0 ? 0 : $quality);
        $this->size = $size;
        $this->extensio = $this->getExtensio();
        $this->nom = $this->getNom($id);
        $this->upload_dir_temp = \Yii::getAlias('@webroot') . '\images\uploads\temporal\\';
        $this->upload_dir = $this->getDirectori();
        $this->upload_dir_nom = null;
        $this->upload_dir_nom_thumb = null;
        $this->storage_dir = $this->getStorageDir();
        $this->qualityType = $this->getQualityType();
        $this->qualityConvertida = $this->getQualityConvertida();
        $this->guardar($id);
        $this->guardarThumbnailQuadrat();
    }

    private function getIndex()
    {

        switch ($this->tipo) {
            case $this::NOTICIA:
                return 'Noticia';
                break;
            default:
                return 'General';
                break;
        }
    }

    private function getExtensio()
    {
        return pathinfo($this->imatge[$this->index]['name']['imatge'], PATHINFO_EXTENSION);
    }

    private function getNom($id)
    {
        return $id . '-' . $this->tipo . '.' . $this->extensio;
    }

    private function getDirectori()
    {
        return \Yii::getAlias('@webroot') . '\images\uploads\\' . $this->tipo . '\\';
    }

    private function getStorageDir()
    {
        return '\images\uploads\\' . $this->tipo . '\\';
    }

    private function getQualityType()
    {
        switch (strtolower($this->extensio)) {
            case 'jpg':
            case 'jpeg':
                return 'jpeg_quality';
                break;
            case 'png':
                return 'png_compression_level';
                break;
        }
    }

    private function getQualityConvertida()
    {
        if ($this->qualityType == 'jpeg_quality') {
            return $this->quality;
        } else if ($this->qualityType == 'png_compression_level') {
            $qualfinal = 9;
            if ($this->quality > 90) $this->quality = $this->quality - 10;
            return (int)($this->quality / 10);
        }

    }

    private function guardar($id)
    {
        $this->upload_dir_nom = $this->upload_dir . $this->nom;
        move_uploaded_file($this->imatge[$this->index]["tmp_name"]['imatge'], $this->upload_dir . $this->nom);

        switch ($this->tipo) {
            case $this::NOTICIA:
                $noticia = \app\models\Noticia::findOne($id);
                $noticia->imatge = $this->storage_dir . $this->nom;
                $noticia->save(false);
                break;
            default:
                return 'General';
                break;
        }

    }

    private function guardarThumbnailQuadrat()
    {
        Image::thumbnail($this->upload_dir_nom, 250, 250)->save($this->upload_dir . 'thumb-' . $this->nom, [$this->qualityType => $this->qualityConvertida]);
        $this->upload_dir_nom_thumb = $this->upload_dir . 'thumb-' . $this->nom;
    }

    static function josepUploadFile($file)
    {

    }

}
