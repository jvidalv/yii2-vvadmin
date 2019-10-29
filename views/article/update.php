<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use dosamigos\tinymce\TinyMce;
use app\models\Language;


/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="welcome p-t-10 pb-5">
    <div class="container">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col col-lg-8">
                <?= $form->field($model, 'title', ['template' => '{input}', 'options' => ['class' => 'form-group']])->textInput(['maxlength' => true, 'class' => 'form-control flex-grow-1', 'placeholder' => '(BORRADOR)']) ?>
            </div>
            <div class="col d-flex">
                <?= Html::submitButton( '💾 ' . Yii::t('app', 'save'), ['class' => 'au-btn au-btn-icon au-btn--green flex-grow-1 mb-4']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col col-lg-8">
                <?= $form->field($model, 'content', ['template' => '<div class="container-content">{input}</div>'])->widget(TinyMce::className(), [
                    'options' => ['rows' => 30],
                    'language' => Yii::$app->language,
                    'clientOptions' => [
                        'width' => "-webkit-fill-available",
                        'height' => "1200px",

                        'plugins' => [
                            "print preview fullpage paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",
                        ],
                        'menubar' => 'file edit view insert format tools table help',
                        'toolbar' => "undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl"
                    ]
                ])
              ?>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong><?= Yii::t('app', 'configuration') ?></strong>
                    </div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php ActiveForm::end(); ?>