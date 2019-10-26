<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Media */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="media-form">
  <div id="alert-media-form" class="alert alert-success" role="alert" style="display:none">
		Dades<strong> guardades </strong>correctament!
  </div>
  <div class="row">
    <div class="col-lg-4">
      <img src="<?= $model->getUrlImatge(250) ?>" />
    </div>
    <div class="col">
      <?php $form = ActiveForm::begin([
              'action' => ['/media/update-ajax?id=' . $model->id],
              'options' => [
                  'id' => 'media-form'
              ]
          ]); ?>
        <label>Direcció de la imatge: </label>
        <input class="w-100 p-2 mb-2" type="text" value="<?= 'https://'. $_SERVER['SERVER_NAME'] .  $model->url ?>" disabled/>

        <?= $form->field($model, 'titol')->textInput(['required' => true]) ?>
        <?= $form->field($model, 'descripcio')->textArea() ?>

        <div class="d-flex mx-auto">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        </div>
      <?php ActiveForm::end(); ?>

    </div>
  </div>
</div>
