<?php

use app\libraries\JosepGridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Users list');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
  <div class="card">
    <div class="card-header d-flex">
        <h1 class="title-1"><?=Html::encode($this->title)?></h1>
        <button type="button" class="au-btn au-btn-icon au-btn--green au-btn--small ml-auto" data-toggle="modal" data-target="#modal">
          <?=Yii::t('app', 'Afegir un usuari')?>
        </button>
    </div>
    <div class="card-body">
      <div class="table-data__tool">
          <div class="table-data__tool-left">
          </div>
          <div class="table-data__tool-right">
            <form>
              <div class="input-group">
                  <div class="input-group-btn">
                      <button class="btn btn-primary">
                          <i class="fa fa-search"></i> <?=Yii::t('app', 'Filtrar')?>
                      </button>
                  </div>
                  <input type="text" name="UserSearch[general]" value="<?=$searchModel['general']?>" onblur="insertParam('UserSearch[general]', this.value  )" placeholder="<?=Yii::t('app', '...')?>" class="form-control">
              </div>
            </form>
          </div>
      </div>
    <?=JosepGridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'app\libraries\JosepActionColumn'],
        [
            'attribute' => 'email',
            'format' => 'raw',
            'value' => function ($model, $key, $index) {
                return '<a href="mailto:' . $model->email . '" target="_blank">' . $model->email . '</a>';
            },
        ],
        [
            'attribute' => 'nom',
            'format' => 'raw',
            'value' => function ($model, $key, $index) {
                return $model->nomComplet;
            },
        ],
        [
            'attribute' => 'activat',
            'format' => 'raw',
            'value' => function ($model, $key, $index) {
                return $model->actiu ? Html::tag('span', Yii::t('app', 'yes'), ['class' => 'btn btn-success']) : Html::tag('span', Yii::t('app', 'no'), ['class' => 'btn btn-warning']);
            },
        ],
        [
            'attribute' => 'role',
            'format' => 'raw',
            'value' => function ($model, $key, $index) {
                return '<span class="role admin">' . $model->rolString . '</span>';
            },
        ],
        [
            'attribute' => 'language_id',
            'format' => 'raw',
            'value' => function ($model, $key, $index) {
                return '<img src="images/' . $model->language->code . '.png" />';
            },
        ],
    ],
]);?>
    </div>
  </div>
</div>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true">
  <?php $form = ActiveForm::begin([
    'fieldConfig' => [
        'options' => ['class' => 'form-group row'],
        'template' => "<div class=\"col col-md-3\">{label}</div>\n<div class=\"col-12 col-md-9\">{input}<small class=\"help-block form-text c-red\">{error}</small></div>\n",
    ],
])?>
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="largeModalLabel"><?=Yii::t('app', 'Afegir un usuari')?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <?=$form->field($model, 'email')->textInput(['maxlength' => true])?>
          <?=$form->field($model, 'nom')->textInput(['maxlength' => true])?>
          <?=$form->field($model, 'cognoms')->textInput(['maxlength' => true])?>
          <?=$form->field($model, 'password')->textInput(['maxlength' => true, 'type' => 'text'])?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
        <?=Html::submitButton(Yii::t('app', 'Afegir'), ['class' => 'btn btn-primary btn-sm'])?>
      </div>
    </div>
  </div>
  <?php ActiveForm::end();?>
</div>
