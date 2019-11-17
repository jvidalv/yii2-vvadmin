<?php

use app\libraries\JosepGridView;
use app\models\Category;
use app\models\Language;
use app\models\Media;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'articles');
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
<section class="pt-5">
    <div class="container">
        <div class="article-index">
            <div class="table-data__tool">
                <div class="table-data__tool-left">
                </div>
                <div class="table-data__tool-right">
                    <form>
                        <div class="input-group">
                            <div class="input-group-btn">
                                <?= Html::button(Yii::t('app', 'create article'), ['type' => 'button', 'class' => 'btn btn-success', 'data' => ['toggle' => 'modal', 'target' => '#modal']]) ?>
                                <button class="btn btn-primary">
                                    <i class="fa fa-search"></i> <?= Yii::t('app', 'Filtrar') ?>
                                </button>
                            </div>
                            <input type="text" name="ArticleSearch[general]" value="<?= $searchModel['general'] ?>"
                                   onblur="insertParam('ArticleSearch[general]', this.value  )"
                                   placeholder="<?= Yii::t('app', '...') ?>" class="form-control">
                        </div>
                    </form>
                </div>
            </div>
            <?php Pjax::begin(); ?>
            <?= JosepGridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'app\libraries\JosepActionColumn',
                        'contentOptions' => ['style' => 'width:5%;'],
                    ],
                    'title',
                    [
                        'label' => Yii::t('app', 'translations'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            $ca = $model->language_id !== Language::LANG_CA && $model->translations->article_ca ? Html::img(['media/get-language-image', 'code' => Language::LANG_CA], ['class' => 'mr-1']) : '';
                            $es = $model->language_id !== Language::LANG_ES && $model->translations->article_es ? Html::img(['media/get-language-image', 'code' => Language::LANG_ES], ['class' => 'mr-1']) : '';
                            $en = $model->language_id !== Language::LANG_EN && $model->translations->article_en ? Html::img(['media/get-language-image', 'code' => Language::LANG_EN], ['class' => 'mr-1']) : '';
                            return $ca . $es . $en;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'image'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::img(['media/get-image', 'table' => Media::TBL_ARTICLE, 'table_id' => $model->id, 'size' => json_encode([65, 65])]);
                        }

                    ]
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
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
                <h3 class="modal-title" id="largeModalLabel"><?= Yii::t('app', 'create article') ?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'resume')->textArea(['maxlength' => true, 'rows' => 2]) ?>
                <?= $form->field($model, 'user_id', ['options' => ['class' => 'd-none']])->hiddenInput(['maxlength' => true, 'value' => Yii::$app->user->identity->id])->label(false) ?>
                <?= $form->field($model, 'translating', ['options' => ['class' => 'd-none']])->hiddenInput(['maxlength' => true, 'value' => 1])->label(false) ?>
                <?= $form->field($model, 'language_id', ['options' => ['class' => 'd-none']])->hiddenInput(['maxlength' => true, 'value' => Yii::$app->user->identity->language_id])->label(false) ?>
                <?= $form->field($model, 'category_id', ['options' => ['class' => 'd-none']])->hiddenInput(['maxlength' => true, 'value' => Category::DEFAULT])->label(false) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm"
                        data-dismiss="modal"><?= Yii::t('app', 'cancel') ?></button>
                <?= Html::submitButton(Yii::t('app', 'afegir'), ['class' => 'btn btn-primary btn-sm']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>