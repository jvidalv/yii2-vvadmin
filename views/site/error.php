<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;


$this->title = Yii::t('app', 'there has been an error in our server');

$this->params['breadcrumbs'][] = $this->title;

?>
<section class="pt-2">
    <div class="container">
        <div class="site-error text-center ">
            <h1 class="my-5"><?= Html::encode($this->title) ?></h1>
            <div class="alert alert-danger">
                <?= nl2br(Html::encode($message)) ?>
            </div>
            <h2 class="my-5">
                <?=  Yii::t('app', 'the above error occurred while the Web server was processing your request.') ?>
            </h2>
            <h4 class="mt-5">
                <?=  Yii::t('app', 'please contact us if you think this is a server error. Thank you.') ?>
            </h4>
        </div>
    </div>
</section>
