<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AppAsset;
use app\models\Language;
use app\models\Media;
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
                            <img src="/images/logo.png" alt="vblogv logo">
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
                                    <?= Html::img(['media/get-image', 'table' => Media::TBL_USER, 'table_id' => Yii::$app->user->identity->id, 'size' => json_encode([45, 45])], ['style' => 'width:45px;height:45px;background:black;']) ?>
                                </div>
                                <div class="content">
                                    <a class="js-acc-btn" href="#"><?= $user->nom ?></a>
                                </div>
                                <div class="account-dropdown js-dropdown">
                                    <div class="info clearfix">
                                        <div class="image">
                                            <a href="/">
                                                <?= Html::img(['media/get-image', 'table' => Media::TBL_USER, 'table_id' => Yii::$app->user->identity->id, 'size' => json_encode([65, 65])]) ?>
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
        <div class="page-content--bgf7 mb-5">
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
            <?= $content ?>
        </div>
        <!-- Position it -->
        <div aria-live="polite" aria-atomic="true" style="position: absolute; top: 90px; right: 15px;">
            <?php foreach (Yii::$app->session->getAllFlashes() as $type => $messages): ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
                    <div class="toast-header">
                        <span class="rounded mr-2 bg-<?= $type ?>" style="width:15px;height:15px"></span>
                        <strong class="mr-auto"><?= Yii::t('app', 'atention') ?></strong>
                        <small class="text-muted text-lowercase"><?= Yii::t('app', 'just now') ?></small>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        <?php if (is_array($messages)): ?>
                            <?php foreach ($messages as $m): ?>
                                <?= $m ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <?= $messages ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>