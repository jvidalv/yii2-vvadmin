<?php
/* @var $form app\models\Article */
/* @var $source app\models\ArticleHasSources */
/* @var $index integer */

use app\models\ArticleHasSources;

?>

<div class="d-flex">
    <?php
    echo $form->field($source, "[$index]id")->hiddenInput()->label(false);
    echo $form->field($source, "[$index]article_id")->hiddenInput()->label(false);
    echo $form->field($source, "[$index]type")->dropDownList(ArticleHasSources::getTypes(), ['prompt' => $source->getAttributeLabel('type')])->label(false);
    echo $form->field($source, "[$index]name")->textInput(['maxlength' => true, 'placeholder' => $source->getAttributeLabel('name')])->label(false);
    echo $form->field($source, "[$index]version")->textInput(['maxlength' => true, 'placeholder' => $source->getAttributeLabel('version')])->label(false);
    echo $form->field($source, "[$index]url")->textInput(['maxlength' => true, 'placeholder' => $source->getAttributeLabel('url')])->label(false);
    echo $form->field($source, "[$index]visible")->dropDownList([0 => Yii::t('app', 'no'), 1 => Yii::t('app', 'yes')], ['prompt' => $source->getAttributeLabel('visible')])->label(false);
    ?>
</div>