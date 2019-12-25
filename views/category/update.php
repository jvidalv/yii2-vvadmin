<?php

use app\models\Media;
use kartik\file\FileInput;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'fieldConfig' => [
        'options' => ['class' => 'form-group row'],
        'template' => "<div class=\"col col-md-4\">{label}</div>\n<div class=\"col-12 col-md-8\">{input}<small class=\"help-block form-text c-red\">{error}</small></div>\n",
    ]
]); ?>

<?php
echo Html::tag('label', Yii::t('app', 'upload an image for the category'));
echo FileInput::widget([
    'name' => 'media_upload[]',
    'id' => 'media-input',
    'options' => [
        'accept' => '.jpeg,.jpg,.png',
    ],
    'language' => Yii::$app->user->identity->language_id,
    'pluginOptions' => [
        'showPreview' => false,
        'uploadUrl' => Url::to(['/media/upload-files',
            'table_id' => $model->id,
            'table_name' => Media::TBL_CATEGORY,
        ]),
        'deleteUrl' => Url::to(['/media/delete-files',
            'table_id' => $model->id,
            'table_name' => Media::TBL_CATEGORY,
        ]),
        'showRemove' => false,
        'maxFileSize' => 300,
    ]
]);
echo '<br>';
?>

<?= $form->field($model, 'name_ca')->textInput(['maxlength' => true])->label('name_ca') ?>

<?= $form->field($model, 'name_es')->textInput(['maxlength' => true])->label('name_es') ?>

<?= $form->field($model, 'name_en')->textInput(['maxlength' => true])->label('name_en') ?>

<?= $form->field($model, 'description_ca')->textarea()->label('desc_ca') ?>

<?= $form->field($model, 'description_es')->textarea()->label('desc_es') ?>

<?= $form->field($model, 'description_en')->textarea()->label('desc_en') ?>

<?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'priority')->textInput(['type' => 'number', 'max' => 9, 'min' => 1, 'value' => 9]) ?>

<?= $form->field($model, 'color_hex')->textInput(['type' => 'color']) ?>




<?= Html::submitButton('💾 ' . Yii::t('app', 'save'), ['class' => "au-btn au-btn-icon au-btn--green au-btn--small float-right", 'data' => ['ajax' => '1']]) ?>

<?php ActiveForm::end(); ?>
