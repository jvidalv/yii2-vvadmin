<?php

use app\assets\ArticleAsset;
use app\models\Article;
use app\models\Category;
use app\models\Language;
use dosamigos\tinymce\TinyMce;
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
            <?php $form = ActiveForm::begin([
                'action' => Url::to(['article/update', 'id' => $model->id])])
            ?>
            <div class="row">
                <div class="col col">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'form-control flex-grow-1', 'placeholder' => Yii::t('app', 'title')]) ?>
                </div>

            </div>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <?= $form->field($model, 'content', ['template' => '<div class="container-content">{input}</div>'])->widget(TinyMce::className(), [
                        'options' => ['rows' => 30],
                        'language' => Yii::$app->language,
                        'clientOptions' => [
                            'width' => "-webkit-fill-available",
                            'min_height' => 600,
                            'plugins' => [
                                "print preview paste searchreplace autolink autosave save code visualblocks visualchars image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons autoresize",
                            ],
                            'link_context_toolbar' => true,
                            'menubar' => 'file edit view insert format tools table help',
                            'toolbar' => "undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl"
                        ]
                    ])
                    ?>
                    <?= $form->field($model, 'tags_form')->textInput(['maxlength' => true, 'value' => $model->getTagsString(), 'placeholder' => Yii::t('app', 'separate them using a comma, ej: react, html')]) ?>
                    <?= FileInput::widget([
                        'name' => 'media_upload[]',
                        'id' => 'media-input',
                        'options' => [
                            'multiple' => true,
                            'accept' => '.jpeg,.jpg,.png'
                        ],
                        'language' => Yii::$app->user->identity->language_id,
                        'pluginOptions' => [
                            'initialPreview' => [
                                $model->media_id ? '/'. $model->media->getUrlImatge() : '/images/defaults/article.png',
                            ],
                            'initialPreviewAsData' => true,
                            'uploadUrl' => Url::to(['/media/upload-files',
                                'id' => $model->id,
                                'tipo' => 'article',
                            ]),
                            'deleteUrl' => Url::to(['/media/delete-files',
                                'id' => $model->id,
                                'tipo' => 'article',
                            ]),
                            'showRemove' => false,
                            'maxFileSize' => 300,
                        ]
                    ]) ?>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <strong><?= Yii::t('app', 'translations') ?></strong>
                        </div>
                        <div class="card-body">
                            <div class="translations-body">
                                <?php foreach (Language::find()->all() as $lang): ?>
                                    <?php if ($lang->code !== $model->language_id): ?>
                                        <div class="d-flex <?= $lang->code === Yii::$app->language ? 'active' : '' ?>">
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
                            <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(Category::find()->all(), 'id', 'name_' . $model->language->code)) ?>
                            <?= $form->field($model, 'resume')->textArea(['maxlength' => true, 'rows' => 2]) ?>

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
                            <div class="card-body">
                                <?php foreach($model->articleHasAnchors as $anchor){
                                    echo Html::a($anchor->content, '#' . $anchor->anchor_id) . '</br>';
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </section>
<?php ActiveForm::end(); ?>