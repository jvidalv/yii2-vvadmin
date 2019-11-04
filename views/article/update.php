<?php

use app\assets\ArticleAsset;
use app\models\ArticleHasTranslations;
use app\models\Language;
use app\models\Category;
use app\models\Article;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use dosamigos\tinymce\TinyMce;
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
            'action' => Url::to(['article/update', 'id' => $model->id] )])
            ?>
        <div class="row">
            <div class="col col">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'form-control flex-grow-1', 'placeholder' => Yii::t('app','title')]) ?>
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
                            "print preview fullpage paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons autoresize",
                        ],
                        'menubar' => 'file edit view insert format tools table help',
                        'toolbar' => "undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl"
                    ]
                ])
              ?>
                <?= $form->field($model, 'tags')->textInput(['maxlength' => true, 'value' => $model->getTagsString(), 'placeholder' => Yii::t('app', 'separate them using a comma, ej: react, html')]) ?>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong><?= Yii::t('app', 'translations') ?></strong>
                    </div>
                    <div class="card-body">
                        <div class="translations-body">
                            <?php  foreach(Language::find()->all() as $lang): ?>
                            <?php if($lang->id !==  $model->language_id): ?>
                            <div class="d-flex <?= $lang->code === Yii::$app->language ? 'active' : '' ?>">
                                <div><img src="/images/<?= $lang->code ?>.png"></div>
                                <?php 
                                $translations = $model->translations;
                                if($translations && $translations['article_'.$lang->code.'_id']): 
                                    $artLang = Article::findOne($translations['article_'.$lang->code.'_id']);?>
                                <div> <?= $artLang->updatedAt ?> </div>
                                <div> <?= $artLang->user->username ?> </div>
                                <div class="ml-auto"> <a href="<?= Url::to(['article/update', 'id' => $model->translations['article_'.$lang->code.'_id'], 'slug' => $model->slug]) ?>"
                                        class="item"><i class="zmdi zmdi-edit" alt="<?= Yii::t('app', 'create') ?>"></i>
                                    </a></div>
                                <?php else: ?>
                                <div> <?= Yii::t('app', 'no translation yet') ?> </div>
                                <div class="ml-auto">
                                    <a href="<?= Url::to(['article/new-translation', 'id' => $model->id, 'lang_code' => $lang->code]) ?>"
                                        class="item"><i class="zmdi zmdi-plus" alt="<?= Yii::t('app', 'create') ?>"></i>
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
                        <strong><?= Yii::t('app', 'Others') ?></strong>
                    </div>
                    <div class="card-body">
                        <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(Category::find()->all(), 'id', 'name_'.$model->language->code)) ?>
                    </div>
                </div>
                <div class="sticky-top">
                    <div class="d-flex">
                        <?= Html::submitButton( '💾 ' . Yii::t('app', 'save'), ['class' => 'au-btn au-btn-icon au-btn--green justify-content-end flex-grow-1 mb-4']) ?>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <strong><?= Yii::t('app', 'sections') ?></strong>
                        </div>
                        <div class="card-body">
                            todo//
                        </div>
                    </div>

                </div>
            </div>
        </div>
</section>
<?php ActiveForm::end(); ?>