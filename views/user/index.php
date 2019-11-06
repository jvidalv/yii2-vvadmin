<?php

use app\libraries\JosepGridView;
use app\models\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'users');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="welcome p-t-10">
    <div class="container">
        <div class="table-data__tool">
            <div class="table-data__tool-left">
                <h1 class="title-1"><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="table-data__tool-right">
                <form>
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal">
                                <?= Yii::t('app', 'Afegir un usuari') ?>
                            </button>
                            <button class="btn btn-primary">
                                <i class="fa fa-search"></i> <?= Yii::t('app', 'Filtrar') ?>
                            </button>
                        </div>
                        <input type="text" name="UserSearch[general]" value="<?= $searchModel['general'] ?>"
                               onblur="insertParam('UserSearch[general]', this.value  )"
                               placeholder="<?= Yii::t('app', '...') ?>" class="form-control">
                    </div>
                </form>
            </div>
        </div>
        <?= JosepGridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'email',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index) {
                        return '<a href="mailto:' . $model->email . '" target="_blank">' . $model->email . '</a>';
                    },
                ],
                [
                    'attribute' => 'nom',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index) {
                        return $model->fullName;
                    },
                ],
                [
                    'attribute' => 'activat',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index) {
                        return $model->actiu ? Html::tag('span', Yii::t('app', 'yes'), ['class' => 'btn btn-success']) : Html::tag('span', Yii::t('app', 'no'), ['class' => 'btn btn-warning']);
                    },
                ],
                [
                    'attribute' => 'role',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index) {
                        return '<span class="role admin">' . $model->rolString . '</span>';
                    },
                ],
                [
                    'attribute' => 'language_id',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index) {
                        return Html::img(['media/get-language-image', 'code' => Yii::$app->user->identity->language_id]);
                    },
                ],
                ['class' => 'app\libraries\JosepActionColumn'],

            ],
        ]); ?>
    </div>
</section>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'options' => ['class' => 'form-group row'],
            'template' => "<div class=\"col col-md-3\">{label}</div>\n<div class=\"col-12 col-md-9\">{input}<small class=\"help-block form-text c-red\">{error}</small></div>\n",
        ],
    ]) ?>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="largeModalLabel"><?= Yii::t('app', 'Afegir un usuari') ?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'nom')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'cognoms')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'password')->textInput(['maxlength' => true, 'type' => 'text']) ?>
                <?= $form->field($model, 'language_id')->dropDownList(ArrayHelper::map(Language::find()->all(), 'code', 'name')) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm"
                        data-dismiss="modal"><?= Yii::t('app', 'cancel') ?></button>
                <?= Html::submitButton(Yii::t('app', 'Afegir'), ['class' => 'btn btn-primary btn-sm']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>