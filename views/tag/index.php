<?php

use app\libraries\JosepGridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="welcome p-t-10">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="title-4"><?= Html::encode($this->title) ?>
                </h1>
                <hr class="line-seprate">
            </div>
        </div>
    </div>
</section>
<section class="pt-4">
    <div class="container">
        <div class="tag-index">
            <div class="row">
                <div class="col-lg-5">
                <?php $form = ActiveForm::begin([
                            'fieldConfig' => [
                                'options' => ['class' => 'form-group row'],
                                'template' => "<div class=\"col col-md-4\">{label}</div>\n<div class=\"col-12 col-md-8\">{input}<small class=\"help-block form-text c-red\">{error}</small></div>\n",
                                ]
                            ]); ?>
                    <div class="card">
                        <div class="card-header d-flex">
                            <h1 class="title-1"><?=Yii::t('app', 'create new')?></h1>
                            <?= Html::submitButton( '💾 ' .Yii::t('app', 'save'), ['class' => "au-btn au-btn-icon au-btn--green au-btn--small ml-auto"]) ?>
                        </div>
                        <div class="card-body">

                            <?= $form->field($model, 'name_ca')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'name_es')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'priority')->textInput(['type' => 'number', 'max' => 9, 'min' => 1, 'value' => 9 ]) ?>

                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header d-flex">
                            <h1 class="title-1"><?= Yii::t('app', 'list') ?></h1>
                            <button type="button" class="au-btn au-btn-icon au-btn--green au-btn--small ml-auto"
                                data-toggle="modal" data-target="#modal">
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
                                 //   ['class' => 'app\libraries\JosepActionColumn'],
                                    'name_' . Yii::$app->user->identity->language->code,
                                ],
                            ]);?>
                            <?php Pjax::end();?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>