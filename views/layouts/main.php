<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AppAsset;
use app\models\Language;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);

$user = Yii::$app->user->identity;

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/images/favicon.png"
              type="image/x-icon"/>
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body>
    <?php $this->beginBody() ?>
    <div class="page-wrapper">
        <header class="header-desktop3 d-none d-lg-block">
            <div class="section__content section__content--p35">
                <div class="header3-wrap">
                    <div class="header__logo">
                        <a href="/">
                            <img src="/images/vblogv-logo.png" alt="vblogv logo">
                        </a>
                    </div>
                    <div class="header__navbar">
                        <ul class="list-unstyled">
                            <li class="has-sub">
                                <?= Html::a('<i class="fas fa-font"></i> ' . Yii::t('app', 'articles') . '<span class="bot-line"></span>', Url::to(['/article/index'])); ?>
                                <ul class="header3-sub-list list-unstyled">
                                    <li>
                                        <?= Html::a('<i class="fas fa-shapes"></i> ' . Yii::t('app', 'categories'), Url::to(['/category/index'])); ?>
                                    </li>
                                    <li>
                                        <?= Html::a('<i class="fas fa-tags"></i> ' . Yii::t('app', 'tags'), Url::to(['/tag/index'])); ?>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <?= Html::a('<i class="fas fa-images"></i> ' . Yii::t('app', 'media') . '<span class="bot-line"></span>', Url::to(['/media/index'])); ?>
                            </li>
                            <li class="has-sub">
                                <a href="#">
                                    <i class="fas fa-desktop"></i>
                                    <span class="bot-line"></span>Admin</a>
                                <ul class="header3-sub-list list-unstyled">
                                    <li>
                                        <?= Html::a('<i class="fas fa-users"></i> ' . Yii::t('app', 'users'), Url::to('/user/index')); ?>
                                    </li>
                                    <li>
                                        <?= Html::a('<i class="fas fa-blog"></i> ' . Yii::t('app', 'blogs'), Url::to('/')); ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="header__tool">
                        <div class="header-button-item js-item-menu">
                            <?= Html::img(['media/get-language-image', 'code' => Yii::$app->user->identity->language_id]) ?>
                            <div class="setting-dropdown js-dropdown">
                                <div class="account-dropdown__body">
                                    <div class="account-dropdown__item">
                                        <?= Html::a(Html::img(['media/get-language-image', 'code' => Language::LANG_CA], ['class' => 'mr-2']) . Yii::t('app', 'catalan') . '</a>', Url::to('/user/change-language'), ['data' => ['method' => 'post', 'params' => ['lang' => 'ca']], 'class' => Yii::$app->language === 'ca' ? 'active-language' : '']) ?>
                                    </div>
                                    <div class="account-dropdown__item">
                                        <?= Html::a(Html::img(['media/get-language-image', 'code' => Language::LANG_ES], ['class' => 'mr-2']) . Yii::t('app', 'spanish') . '</a>', Url::to('/user/change-language'), ['data' => ['method' => 'post', 'params' => ['lang' => 'es']], 'class' => Yii::$app->language === 'es' ? 'active-language' : '']) ?>

                                    </div>
                                    <div class="account-dropdown__item">
                                        <?= Html::a(Html::img(['media/get-language-image', 'code' => Language::LANG_EN], ['class' => 'mr-2']) . Yii::t('app', 'english') . '</a>', Url::to('/user/change-language'), ['data' => ['method' => 'post', 'params' => ['lang' => 'en']], 'class' => Yii::$app->language === 'en' ? 'active-language' : '']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="account-wrap">
                            <div class="account-item account-item--style2 clearfix js-item-menu">
                                <div class="image">
                                    <?= Html::img(['media/get-user-image', 'id' => Yii::$app->user->identity->id]) ?>
                                </div>
                                <div class="content">
                                    <a class="js-acc-btn" href="#"><?= $user->nom ?></a>
                                </div>
                                <div class="account-dropdown js-dropdown">
                                    <div class="info clearfix">
                                        <div class="image">
                                            <a href="/">
                                                <?= Html::img(['media/get-user-image', 'id' => Yii::$app->user->identity->id]) ?>
                                            </a>
                                        </div>
                                        <div class="content">
                                            <h5 class="name">
                                                <a href="#"><?= $user->fullName ?></a>
                                            </h5>
                                            <span class="email"><?= $user->email ?></span>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__body">
                                        <div class="account-dropdown__item">
                                            <?= Html::a('<i class="zmdi zmdi-edit"></i>' . Yii::t('app', 'Perfil'), Url::to(['/user/update', 'id' => Yii::$app->user->identity->id, 'slug' => Yii::$app->user->identity->slug])); ?>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__footer">
                                        <?= Html::a('<i class="zmdi zmdi-account-o"></i>' . Yii::t('app', 'Logout'), Url::to('/site/logout'), ['data' => ['method' => 'post', 'confirm' => Yii::t('app', 'Sure about leaving?')]]); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="page-content--bgf7">
            <?php if (isset($this->params['breadcrumbs'])): ?>
                <section class="au-breadcrumb2">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="au-breadcrumb-content">
                                    <div class="au-breadcrumb-left">
                                        <span class="au-breadcrumb-span"><?= Yii::t('app', 'Estas aquí:') ?></span>
                                        <?= Breadcrumbs::widget([
                                            'homeLink' => ['label' => Yii::t('app', 'Inici'), 'url' => '/'],
                                            'options' => ['class' => 'list-unstyled list-inline au-breadcrumb__list'],
                                            'itemTemplate' => "<li class=\"list-inline-item old\">{link}</li>\n",
                                            'links' => $this->params['breadcrumbs'],
                                            'activeItemTemplate' => "<li class=\"list-inline-item active\">{link}</li>\n",
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <section class="flash-zone">
                    <div class="container">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= Yii::$app->session->getFlash('success'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                    </div>
                </section>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('danger')): ?>
                <section class="flash-zone">
                    <div class="container">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php foreach(Yii::$app->session->getFlash('danger') as $error){
                                echo $error;
;                            } ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                    </div>
                </section>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </div>
    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage() ?>