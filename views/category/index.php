<?php

use app\libraries\JosepGridView;
use app\models\Media;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'categories');
$this->params['breadcrumbs'][] = $this->title;
?>
    <section class="pt-2">
        <div class="container">
            <div class="category-index">
                <div class="row">
                    <div class="col-lg-4">
                        <?php $form = ActiveForm::begin([
                            'fieldConfig' => [
                                'options' => ['class' => 'form-group row'],
                                'template' => "<div class=\"col col-md-4\">{label}</div>\n<div class=\"col-12 col-md-8\">{input}<small class=\"help-block form-text c-red\">{error}</small></div>\n",
                            ]
                        ]); ?>
                        <div class="card">
                            <div class="card-header d-flex">
                                <strong><?= Yii::t('app', 'create new category') ?></strong>
                            </div>
                            <div class="card-body">
                                <?= $form->field($model, 'name_ca')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label('name_ca') ?>
                                <?= $form->field($model, 'name_es')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label('name_es') ?>
                                <?= $form->field($model, 'name_en')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label('name_en') ?>
                                <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
                                <?= $form->field($model, 'priority')->textInput(['type' => 'number', 'max' => 9, 'min' => 1, 'value' => 9]) ?>
                            </div>
                            <div class="card-footer">
                                <?= Html::submitButton('💾 ' . Yii::t('app', 'save'), ['class' => "au-btn au-btn-icon au-btn--green au-btn--small ml-auto"]) ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="col-lg-8">

                        <?php Pjax::begin(); ?>
                        <?= JosepGridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                //   ['class' => 'app\libraries\JosepActionColumn'],
                                'code',
                                'name_' . Yii::$app->user->identity->language->code,
                                //'description_' . Yii::$app->user->identity->language->code,
                                [
                                    'label' => Yii::t('app', '# of articles'),
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return count($model->articles);
                                    }
                                ],
                                //'priority',
                                [
                                    'label' => Yii::t('app', 'image'),
                                    'format' => 'raw',
                                    'contentOptions' => ['style' => 'width:11%;'],
                                    'value' => function ($model) {
                                        return Html::img(['media/get-image', 'table' => Media::TBL_CATEGORY, 'table_id' => $model->id, 'size' => json_encode([65, 65])], ['style' => 'width:35px; height: 35px;border-bottom:3px solid ' . $model->color_hex]);
                                    }

                                ],
                                [
                                    'class' => 'app\libraries\JosepActionColumn',
                                    'visibleButtons' => ['update' => true, 'delete' => true, 'restaurar' => false],
                                    'buttons' => [
                                        'update' => function ($url, $model, $key) {
                                            return Html::a(Html::tag('i', '', ['class' => 'zmdi zmdi-edit']), '#', ['class' => 'item', 'data' => ['toggle' => 'modal', 'target' => '#modal-update', 'id' => $model->id]]);
                                        },
                                    ]
                                ],
                            ],
                        ]); ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
Modal::begin([
    'title' => Yii::t('app', 'modify category'),
    'id' => 'modal-update',
]);
echo Yii::t('app', 'loading...');
Modal::end();

$this->registerJs(new JsExpression('
    $( "#modal-update" ).on("shown.bs.modal", function(e){
        $(this).find(".modal-body").load("category/update?id=" + $(e.relatedTarget).data("id"));
    });
'));
?>