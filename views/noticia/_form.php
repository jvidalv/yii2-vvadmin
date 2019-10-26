<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use dosamigos\tinymce\TinyMce;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;
use kartik\switchinput\SwitchInput;
use app\assets\NoticiaAsset;

NoticiaAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Noticia */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->titol ? $model->titol : 'Crear una notícia nova';
$this->params['breadcrumbs'][] = ['label' => 'Llistat de notícies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin(); ?>

<div class="noticia-form">
  <?php if(!$model->titol): ?>
   <div class="alert alert-warning mb-4" role="alert">
      <strong>Això es un borrador!</strong> Has de escriure un titol i seleccionar una data per a poder publicar la notícia.
   </div>
  <?php endif; ?>
  <div class="row">
    <div class="col-xl">
      <div class="card">
          <div class="card-body card-block">
              <?= $form->field($model, 'titol')->textInput(['maxlength' => true, 'class' => 'form-control custom-titol', 'placeholder' => '(BORRADOR)']) ?>
              <?= $form->field($model, 'cos')->widget(TinyMce::className(), [
                    'options' => ['rows' => 30],
                    'language' => 'ca',
                    'clientOptions' => [
                        'plugins' => [
                            "advlist autolink lists link charmap print preview anchor",
                            "insertdatetime media table contextmenu paste"
                        ],
                        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    ]
                ])
              ?>
          </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="mb-4">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-lg btn-block']) ?>
      </div>
      <?php if(Yii::$app->user->identity->esAdmin()): ?>
      <div class="card">
        <div class="card-header">
            <strong>Notícia global</strong>
        </div>
        <div class="card-body card-block d-flex m-auto">
          <?= $form->field($model, 'super')->widget(SwitchInput::classname(), [
              'type' => SwitchInput::CHECKBOX,
              'pluginOptions' => [
                  'size' => 'large',
                  'onText' => 'SI',
                  'offText' => 'NO',
                ]
          ])->label(false); ?>
        </div>
      </div>
        <?php endif; ?>
        <div class="card">
          <div class="card-header">
              <strong>Data publicació</strong>
          </div>
          <div class="card-body card-block">
            <?= $form->field($model, 'data_publicacio')->widget(DateTimePicker::classname(), [
                 'options' => ['placeholder' => 'Seleccionar una data'],
                 'type' => DateTimePicker::TYPE_INLINE,
                 'pluginOptions' => [
                     'format' => 'dd-mm-yyyy hh:ii'
                 ]
             ])->label(false); ?>
            <small> * La notícia no es publicara fins aquesta data </small>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
              <strong>Imatge destacada</strong>
          </div>
          <div class="card-body card-block">
            <?= $form->field($model, 'imatge')->widget(FileInput::classname(), [
                'options' => ['accept' => '.jpeg,.jpg,.png'],
                'language' => 'ca',
                'pluginOptions' => [
                  'uploadUrl' => Url::to(['/site/upload-imatge?tipo=noticies&id='.$model->id]),
                  'maxFileSize' => 1024,
                  'initialPreview'=> $model->imatge ? [$model->imatge] : false,
                  'initialPreviewAsData'=> true,
                ]
                ])->label(false) ?>
          </div>
        </div>
    </div>
  </div>
  <!-- <div class="form-group">
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
  </div> -->
</div>
<?php ActiveForm::end(); ?>
