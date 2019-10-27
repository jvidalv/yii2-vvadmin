<?php

use app\libraries\JosepGridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">
    <div class="row">
    <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex">
                    <h1 class="title-1"><?=Yii::t('app', 'create new')?></h1>
                    <button type="button" class="au-btn au-btn-icon au-btn--blue au-btn--small ml-auto" data-toggle="modal" data-target="#modal">
                        <?=Yii::t('app', 'save')?>
                    </button>
                </div>
                <div class="card-body">
                <?php $form = ActiveForm::begin([
  'fieldConfig' => [
      'options' => ['class' => 'form-group row'],
      'template' => "<div class=\"col col-md-3\">{label}</div>\n<div class=\"col-12 col-md-9\">{input}<small class=\"help-block form-text c-red\">{error}</small></div>\n",
      ]
  ]); ?>
    <?= $form->field($model, 'name_ca')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'name_es')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description_ca')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description_es')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description_en')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'priority')->textInput() ?>
<?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex">
                    <h1 class="title-1"><?=Html::encode($this->title)?></h1>
                    <button type="button" class="au-btn au-btn-icon au-btn--green au-btn--small ml-auto" data-toggle="modal" data-target="#modal">
                    <?=Yii::t('app', 'add a category')?>
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-data__tool">
                        <div class="table-data__tool-left">
                        </div>
                        <div class="table-data__tool-right">
                        </div>
                    </div>
                <?php Pjax::begin();?>
                    <?=JosepGridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'app\libraries\JosepActionColumn'],
                            'code',
                            'name_' . Yii::$app->user->identity->language->code,
                            'description_' . Yii::$app->user->identity->language->code,
                        ],
                    ]);?>
                <?php Pjax::end();?>
                </div>
            </div>
        </div>
      
    </div>
</div>
