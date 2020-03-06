<?php

use app\assets\ArticleAsset;
use app\models\Article;
use app\models\Category;
use app\models\Language;
use app\models\Media;
use app\models\Tag;
use dosamigos\tinymce\TinyMce;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

ArticleAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Article */
/* @var $sources app\models\ArticleHasSources */
/* @var $newSource app\models\ArticleHasSources */


$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin() ?>
    <section class="pb-5 mb-5">
        <div class="container">
            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'title')
                        ->textInput([
                            'maxlength' => true,
                            'class' => 'form-control flex-grow-1 ' . ($model->isFeatured() ? 'bg-warning' : ''),
                            'placeholder' => Yii::t('app', 'title')
                        ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-9">
                    <div class="loader-tinymce">
                        <i class="fa fa-spinner fa-spin"></i>
                    </div>
                    <?= $form->field($model, 'content', ['template' => '<div class="container-content" style="display:none">{input}</div>'])->widget(TinyMce::className(), [
                        'options' => ['rows' => 30],
                        'language' => Yii::$app->language,
                        'clientOptions' => [
                            'width' => "-webkit-fill-available",
                            'min_height' => 600,
                            'plugins' => [
                                "print preview paste searchreplace autolink autosave save code visualblocks visualchars wordcount autoresize image link media codesample table hr nonbreaking anchor toc advlist lists noneditable charmap quickbars",
                            ],
                            'codesample_languages' => [
                                ['text' => 'HTML/XML', 'value' => 'markup'],
                                ['text' => 'JavaScript', 'value' => 'javascript'],
                                ['text' => 'CSS', 'value' => 'css'],
                                ['text' => 'PHP', 'value' => 'php'],
                                ['text' => 'JSX', 'value' => 'jsx'],
                                ['text' => 'BASH', 'value' => 'bash'],
                            ],
                            'extended_valid_elements' => 'img[longdesc|usemap|src|border|alt=|title|hspace|vspace|width|height|align|onerror|id|class|style]',
                            'menubar' => 'file edit view insert format tools table help',
                            'toolbar' => "undo redo | bold italic underline strikethrough | image link anchor codesample | numlist bullist | charmap removeformat | code ",
                            'link_context_toolbar' => true,
                            'image_title' => true,
                            'automatic_uploads' => true,
                            'visualblocks_default_state' => true,
                            'file_picker_types' => 'image',
                            'file_picker_callback' => new JsExpression("(cb, value, meta) => uploadImageTiny(cb, value, meta)"),
                            'relative_urls' => false,
                            'remove_script_host' => false,
                            'setup' => new JsExpression('function(editor){ editor.on("init", function (e) {
                               $(".loader-tinymce").fadeOut("slow");
                               $(".container-content").fadeIn("slow");
                            });}'),
                        ]
                    ])
                    ?>
                    <?php
                    //  Ghost content in dom to make anchors inside tinymce work
                    echo Html::beginTag('div', ['id' => 'contents-ghost', 'class' => 'd-none']);
                    echo $model->content;
                    echo Html::endTag('div');
                    ?>

                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <?= Html::tag('strong', Yii::t('app', 'translations')) ?>
                            <span class="cursor-pointer ml-auto" data-target="#modal-sync"
                                  data-toggle="modal">🔁</span>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header cursor-pointer text-center" data-target="#extra-information"
                                     data-toggle="modal">
                                    <strong><?= Yii::t('app', 'extra') ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header cursor-pointer text-center" data-target="#modal-sources"
                                     data-toggle="modal">
                                    <strong><?= Yii::t('app', 'sources') ?> (<?= count($sources) ?>)</strong>
                                </div>
                            </div>
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

<?php
/**
 * Extra info modal begin
 */
Modal::begin([
    'title' => Yii::t('app', 'extra options'),
    'size' => Modal::SIZE_EXTRA_LARGE,
    'id' => 'extra-information',
    'footer' => Html::submitButton('💾 ' . Yii::t('app', 'save'), ['class' => 'au-btn au-btn-icon au-btn--green justify-content-end flex-grow-1'])
]);
echo $form->field($model, 'resume')->textArea(['maxlength' => true, 'rows' => 2]);
echo $form->field($model, 'tags')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(Tag::find()->all(), 'id', "name_$model->language_id"),
    'theme' => Select2::THEME_BOOTSTRAP,
    'options' => [
        'placeholder' => 'Seleccionar tags',
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'multiple' => true,
    ],
]);
echo $form->field($model, 'regenerate_slug')->widget(SwitchInput::class, [
    'pluginOptions' => [
        'onText' => 'Si',
        'offText' => 'No'
    ]
]);

echo $form->field($model, 'continuation')->dropDownList(
    ArrayHelper::map(Article::find()->where(['language_id' => $model->language_id])->andWhere(['not', ['id' => $model->id]])->all()
        , 'id', 'title'), ['prompt' => Yii::t('app', 'select a continuation')]);

echo Html::tag('label', Yii::t('app', 'upload an image for the article'));
echo FileInput::widget([
    'name' => 'media_upload[]',
    'id' => 'media-input',
    'options' => [
        'accept' => '.jpeg,.jpg,.png',
    ],
    'pluginOptions' => [
        'initialPreview' => [
            '/' . Media::img($model->id, Media::TBL_ARTICLE, [200, 200]),
        ],
        'initialPreviewAsData' => true,
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
Modal::end();
/**
 * Extra info modal END
 * Source modal begin
 */
Modal::begin([
    'title' => Yii::t('app', 'sources'),
    'size' => Modal::SIZE_EXTRA_LARGE,
    'id' => 'modal-sources',
    'footer' => Html::submitButton('💾 ' . Yii::t('app', 'save'), ['class' => 'au-btn au-btn-icon au-btn--green justify-content-end flex-grow-1'])
]);
// Limit sources at 7 max
$i = 0;
foreach ($sources as $source) {
    echo $this->render('_source', ['form' => $form, 'source' => $source, 'index' => $i]);
    $i++;
}

while ($i < 7) {
    echo $this->render('_source', ['form' => $form, 'source' => $newSource, 'index' => $i]);
    $i++;
}

Modal::end();
/**
 * Source modal END
 */

ActiveForm::end();
/**
 * Modal sync START
 * Syncronizes common resources between articles
 */
ActiveForm::begin(['action' => Url::to(['article/sync', 'id' => $model->id]), 'id' => 'sync-form']);
Modal::begin([
    'title' => Yii::t('app', 'synchronitzation between languages'),
    'size' => Modal::SIZE_EXTRA_LARGE,
    'id' => 'modal-sync',
    'footer' => Html::submitButton('💫 ' . Yii::t('app', 'sync'), ['class' => 'au-btn au-btn-icon au-btn--blue justify-content-end flex-grow-1'])
]);
echo Html::label(Yii::t('app', 'languages'));
echo Select2::widget([
    'name' => 'sync-languages',
    'data' => $model->getSyncLanguages(),
    'pluginOptions' => [
        'multiple' => true,
        'allowClear' => false,
        'required' => true
    ]]);
echo Html::tag('br');
echo Html::label(Yii::t('app', 'sync elements'));
echo Select2::widget([
    'name' => 'sync-options',
    'data' => [
        'tags' => Yii::t('app', 'tags'),
        'sources' => Yii::t('app', 'sources'),
        'resume' => Yii::t('app', 'resume'),
        'content' => Yii::t('app', 'content')
    ],
    'options' => [
        'placeholder' => Yii::t('app', 'select elements to sync')
    ],
    'pluginOptions' => [
        'multiple' => true,
        'allowClear' => false,
        'required' => true
    ]]);
Modal::end();
ActiveForm::end();

/**
 * Modal sync END
 */

