<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'fieldConfig' => [
        'options' => ['class' => 'form-group row'],
        'template' => "<div class=\"col col-md-4\">{label}</div>\n<div class=\"col-12 col-md-8\">{input}<small class=\"help-block form-text c-red\">{error}</small></div>\n",
    ]
]); ?>
<?= $form->field($model, 'name_ca')->textInput(['maxlength' => true])->label('name_ca') ?>

<?= $form->field($model, 'name_es')->textInput(['maxlength' => true])->label('name_es') ?>

<?= $form->field($model, 'name_en')->textInput(['maxlength' => true])->label('name_en') ?>

<?= $form->field($model, 'priority')->textInput(['type' => 'number', 'max' => 9, 'min' => 1, 'value' => 9]) ?>
<?= Html::submitButton('💾 ' . Yii::t('app', 'save'), ['class' => "au-btn au-btn-icon au-btn--green au-btn--small float-right", 'data' => ['ajax' => '1']]) ?>
<?php ActiveForm::end(); ?>
