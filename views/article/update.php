<?php

use app\assets\ArticleAsset;
use app\models\Article;
use app\models\Category;
use app\models\Language;
use app\models\Media;
use dosamigos\tinymce\TinyMce;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

ArticleAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <section class="pb-5 mb-5">
        <div class="container">
            <?php $form = ActiveForm::begin() ?>
            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'form-control flex-grow-1', 'placeholder' => Yii::t('app', 'title')]) ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <div style="background: url('/<?= Media::img($model->id, Media::TBL_ARTICLE, [1199, 150]) ?> ')"
                         class="cover-no-repeat">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-9">
                    <?= $form->field($model, 'content', ['template' => '<div class="container-content">{input}</div>'])->widget(TinyMce::className(), [
                        'options' => ['rows' => 30],
                        'language' => Yii::$app->language,
                        'clientOptions' => [
                            'width' => "-webkit-fill-available",
                            'min_height' => 600,
                            'plugins' => [
                                "print preview paste searchreplace autolink autosave save code visualblocks visualchars wordcount autoresize image link media codesample table hr nonbreaking anchor toc advlist lists noneditable charmap quickbars",
                            ],
                            'menubar' => 'file edit view insert format tools table help',
                            'toolbar' => "undo redo | bold italic underline strikethrough | image link anchor codesample | numlist bullist | charmap removeformat | code ",
                            'link_context_toolbar' => true,
                            'image_title' => true,
                            'automatic_uploads' => true,
                            'visualblocks_default_state' => true,
                            'file_picker_types' => 'image',
                            'file_picker_callback' => new \yii\web\JsExpression("(cb, value, meta) => uploadImageTiny(cb, value, meta)"),
                        ]
                    ])
                    ?>
                    <?= $form->field($model, 'tags_form')->textInput(['maxlength' => true, 'value' => $model->getTagsString(), 'placeholder' => Yii::t('app', 'separate them using a comma, ej: react, html')]) ?>
                    <?php
                    echo Html::beginTag('div', ['id' => 'contents-ghost', 'class' => 'd-none']);
                    echo $model->content;
                    echo Html::endTag('div');
                    ?>
                    <?php
                    echo Html::tag('label', Yii::t('app', 'upload an image for the article'));
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
                                'table_name' => Media::TBL_ARTICLE,
                            ]),
                            'deleteUrl' => Url::to(['/media/delete-files',
                                'table_id' => $model->id,
                                'table_name' => Media::TBL_ARTICLE,
                            ]),
                            'showRemove' => false,
                            'maxFileSize' => 300,
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <?= Html::tag('strong', Yii::t('app', 'translations')) ?>
                            <?= Html::tag('i', '', ['class' => 'fa fa-arrow-down ml-auto']) ?>
                        </div>
                        <div class="card-body">
                            <div class="translations-body">
                                <?php foreach (Language::find()->all() as $lang): ?>
                                    <?php if ($lang->code !== $model->language_id): ?>
                                        <div class="d-flex">
                                            <div>
                                                <?= Html::img(['media/get-language-image', 'code' => $lang->code]) ?>
                                            </div>
                                            <?php
                                            $translations = $model->translations;
                                            if ($translations && $translations['article_' . $lang->code]):
                                                $artLang = Article::findOne($translations['article_' . $lang->code]); ?>
                                                <div> <?= $artLang->updatedAt ?> </div>
                                                <div> <?= $artLang->user->username ?> </div>
                                                <div class="ml-auto"><a
                                                            href="<?= Url::to(['article/update', 'id' => $model->translations['article_' . $lang->code], 'slug' => $model->slug]) ?>"
                                                            class="item"><i class="zmdi zmdi-edit"
                                                                            alt="<?= Yii::t('app', 'create') ?>"></i>
                                                    </a></div>
                                            <?php else: ?>
                                                <div> <?= Yii::t('app', 'no translation yet') ?> </div>
                                                <div class="ml-auto">
                                                    <a href="<?= Url::to(['article/new-translation', 'id' => $model->id, 'lang_code' => $lang->code]) ?>"
                                                       class="item"><i class="zmdi zmdi-plus"
                                                                       alt="<?= Yii::t('app', 'create') ?>"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <strong><?= Yii::t('app', 'others') ?></strong>
                        </div>
                        <div class="card-body">
                            <?= $form->field($model, 'state')->dropDownList(Article::getStates()) ?>
                            <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(Category::find()->all(), 'id', 'name_' . $model->language->code)) ?>
                            <?= $form->field($model, 'resume')->textArea(['maxlength' => true, 'rows' => 2]) ?>
                            <?= $form->field($model, 'date')->widget(DateTimePicker::class, [
                                'type' => DateTimePicker::TYPE_BUTTON,
                                'layout' => Html::tag('div', '{picker} {input}', ['class' => 'd-flex']),
                                'options' => [
                                    'type' => 'text',
                                    'placeholder' => Yii::t('app', 'select a date'),
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ],
                                'pluginOptions' => [
                                    'format' => 'dd-mm-yyyy hh:ii',
                                    'autoclose' => true,
                                ]
                            ]); ?>

                        </div>
                    </div>
                    <div class="sticky-top">
                        <div class="d-flex">
                            <?= Html::submitButton('💾 ' . Yii::t('app', 'save'), ['class' => 'au-btn au-btn-icon au-btn--green justify-content-end flex-grow-1 mb-4']) ?>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <strong><?= Yii::t('app', 'sections') ?></strong>
                            </div>
                            <div class="card-body overflow-hidden">
                                <?php foreach ($model->articleHasAnchors as $anchor) {
                                    echo Html::a($anchor->content, '#' . $anchor->anchor_id, ['class' => 'anchor-section']) . '</br>';
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </section>
<?php ActiveForm::end(); ?>