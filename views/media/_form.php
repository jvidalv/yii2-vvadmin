<?php

use app\models\Media;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Media */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="media-form">
    <div id="alert-media-form" class="alert alert-success" role="alert" style="display:none">
        <?= Yii::t('app', 'Data saved correctly') ?>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <?= Html::img(['media/get-image', 'table' => Media::TBL_MEDIA, 'table_id' => $model->id, 'size' => json_encode([250, 250])]) ?>
        </div>
        <div class="col">
            <?php $form = ActiveForm::begin([
                'action' => ['/media/update-ajax?id=' . $model->id],
                'options' => [
                    'id' => 'media-form'
                ]
            ]); ?>
            <label><?= Yii::t('app', 'Path to media') ?></label>
            <input class="w-100 p-2 mb-2" type="text" value="<?= 'https://' . $_SERVER['SERVER_NAME'] . $model->url ?>"
                   disabled/>

            <?= $form->field($model, 'titol')->textInput(['required' => true]) ?>
            <?= $form->field($model, 'descripcio')->textArea() ?>

            <div class="d-flex mx-auto">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
