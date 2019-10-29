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
<section class="pt-2">
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
                            <strong><?=Yii::t('app', 'create new tag')?></strong>
                        </div>
                        <div class="card-body">

                            <?= $form->field($model, 'name_ca')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'name_es')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'priority')->textInput(['type' => 'number', 'max' => 9, 'min' => 1, 'value' => 9 ]) ?>

                        </div>
                        <div class="card-footer">
                            <?= Html::submitButton( '💾 ' .Yii::t('app', 'save'), ['class' => "au-btn au-btn-icon au-btn--green au-btn--small float-right"]) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="col-lg-7">
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
</section>