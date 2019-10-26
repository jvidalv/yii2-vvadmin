<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\libraries\JosepGridView;
use kartik\file\FileInput;
use app\assets\MediaAsset;

MediaAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\MediaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Imatges i documents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-index">
  <div class="card">
    <div class="card-header d-flex">
        <h1 class="title-1"><?= Html::encode($this->title) ?></h1>
        <button type="button" class="au-btn au-btn-icon au-btn--green au-btn--small ml-auto" data-toggle="modal" data-target="#modal">
          + Penjar fitxers
        </button>
    </div>
    <div class="card-body">
      <div class="table-data__tool">
          <div class="table-data__tool-left">
            <ul class="nav nav-pills">
  						<li class="nav-item small">
  							<div class="nav-link cursor-pointer <?= $activa == 'totals' ? 'active' : null ?>" onclick="insertParam('MediaSearch[es_imatge]', null)">Totes</div>
  						</li>
  						<li class="nav-item cursor-pointer small">
                <div class="nav-link cursor-pointer <?= $activa == 'imatges' ? 'active' : null ?>" onclick="insertParam('MediaSearch[es_imatge]', 1)">Imatges</div>
  						</li>
              <li class="nav-item cursor-pointer small">
                <div class="nav-link cursor-pointer <?= $activa == 'documents' ? 'active' : null ?>" onclick="insertParam('MediaSearch[es_imatge]', 0)">Documents</div>
  						</li>
  					</ul>
          </div>
          <div class="table-data__tool-right">
            <form>
              <div class="input-group">
                  <div class="input-group-btn">
                      <button class="btn btn-primary">
                          <i class="fa fa-search"></i> <?= Yii::t('app', 'Filtrar') ?>
                      </button>
                  </div>
                  <input type="text" name="MediaSearch[general]" value="<?= $searchModel['general'] ?>" onblur="insertParam('MediaSearch[general]', this.value  )" placeholder="<?= Yii::t('app', '...') ?>" class="form-control">
              </div>
            </form>
          </div>
      </div>
    <div id="recarregar-ajax">
    <?php Pjax::begin(); ?>
    <?= JosepGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions'=>function($model){
                return ['data-imatge-id' => $model->id, 'style' => 'cursor:pointer'];
          },
        'columns' => [
            [
              'class' => 'app\libraries\JosepMediaActionColumn',
              'contentOptions' => ['style' => 'width:5%;'],
            ],
            [
               'format' => 'raw',
               'value' => function ($model, $key, $index) {
                   return  '<img src="' . $model->getUrlImatge(65) . '" />';
               },
            ],
            'titol',
            'descripcio',
            //'destacat',
            //'titol',
            //'descripcio',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
  </div>
</div>
<!-- modal penjar fitxers -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="largeModalLabel">Penjar fitxers</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?= FileInput::widget([
            'name' => 'media_upload[]',
            'id' => 'media-input',
            'options' => [
              'multiple'=>true,
              'accept' => '.xlsx,.pptx,.pdf,.txt,.doc,.dot,.docx,.jpeg,.jpg,.png'],
            'language' => 'ca',
            'pluginOptions' => [
              'uploadUrl' => Url::to(['/media/upload-files?tipo=media']),
              'maxFileSize' => 1024,
            ]
          ]) ?>
      </div>
      </div>
  </div>
</div>
<!-- pujar fitxer modal -->
<!-- modal modificar media -->
<div class="modal fade" id="modal-media" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="largeModalLabel">Modificar metadades de la imatge</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Carregant ...
      </div>
      </div>
  </div>
</div>
<!-- pujar fitxer modal -->
