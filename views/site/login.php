<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\LoginForm */

use app\assets\LoginAsset;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

LoginAsset::register($this);

$this->title = Yii::t('app', 'login');
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="animsition">
<?php $this->beginBody() ?>
<div class="page-wrapper">
    <div class="page-content--bge5 animacio-gradient">
        <div class="container">
            <div class="login-wrap">
                <div class="login-logo">
                    <img src="/images/logo.svg" alt="vblogv" width="200">
                </div>
                <div class="login-content">
                    <div>
                        <?php if (Yii::$app->session->hasFlash('login')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= Yii::$app->session->getFlash('login'); ?>
                            </div>
                        <?php endif; ?>
                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'enableClientValidation' => false,
                            'fieldConfig' => [
                                'template' => "<label class='login-labels mb-0'>{label}</label>{input}",
                            ],
                        ]); ?>
                        <div class="container-pantalles-login">
                            <div id="container-email">
                                <div class="text-center">
                                    <h2><?= Yii::t('app', 'log in') ?></h2>
                                    <p class="mt-1"><?= Yii::t('app', 'use your vvlog account') ?></p>
                                </div>
                                <div class="mt-4">
                                    <div>
                                        <?= $form->field($model, 'email')->textInput(
                                            [
                                                'class' => 'form-control login-input p-4',
                                                'autofocus' => true,
                                                'type' => 'email',
                                                'autocorrect' => 'off',
                                                'required' => true,
                                            ])->label(Yii::t('app', 'email')) ?>
                                        <label id="error-label-email"
                                               class="m-0 float-right small text-danger error-label"></label>
                                    </div>
                                    <div class="d-flex flex-row-reverse flex-wrap w-100 mt-4">
                                        <div>
                                      <span id="seguent-usuari" class="btn btn-primary bg-blau-fosc br cursor-pointer">
                                        <?= Yii::t('app', 'next') ?>
                                      </span>
                                        </div>
                                        <div style="flex-grow: 1">
                                            <a href="https://www.fempoble.app/contactar"
                                               class="text-secondary"><?= Yii::t('app', 'do you need an acount?') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="container-password" style="display:none">
                                <div class="text-center">
                                    <img id="empresa_imatge-response" class="rounded-circle mx-auto d-block w-25">
                                    <p class="mt-2"><?= Yii::t('app', 'Benvingut de nou,') ?> <span id="nom-response"
                                                                                                    class="font-weight-bold">No_value</span>
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <div>
                                        <?= $form->field($model, 'password')->passwordInput(
                                            [
                                                'class' => 'form-control login-input p-4',
                                                'autocomplete' => 'off',
                                                'autocorrect' => 'off',
                                                'required' => true,
                                            ]
                                        )->label(Yii::t('app', 'La seva contrassenya')) ?>
                                        <label id="error-label-password"
                                               class="m-0 float-right small text-danger error-label"></label>
                                    </div>
                                    <div class="d-flex flex-row-reverse flex-wrap w-100 mt-4">
                                        <div>
                                      <span id="seguent-password"
                                            class="btn btn-primary bg-blau-fosc br cursor-pointer">
                                        <?= Yii::t('app', 'Següent') ?>
                                      </span>
                                        </div>
                                        <div style="flex-grow: 1">
                                            <a href="mailto:info@fempoble.app"
                                               class="text-secondary"><?= Yii::t('app', 'No recordes la teva contrassenya?') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <?= Html::submitButton('Connectar-se', ['class' => 'au-btn au-btn--block au-btn--green m-b-20', 'name' => 'login-button']) ?> -->
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
<?php $this->endPage() ?>
</html>
