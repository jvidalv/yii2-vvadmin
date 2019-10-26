<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

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
    <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/images/favicon.png" type="image/x-icon" />
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="">
<?php $this->beginBody() ?>

<div class="page-wrapper">
  <!-- MENU ESCRITORI -->
  <aside class="menu-sidebar2" style="overflow: hidden">
        <div class="logo">
            <a href="/" class="mx-auto">
                <img src="/images/logo.png" alt="Cool Admin">
            </a>
        </div>
        <div class="menu-sidebar2__content js-scrollbar1">
            <div class="account2 p-4">
              <div class="img-cir">
                <img class="round" src="<?= $user->media->getUrlImatge() ?>" alt="<?= $user->nomComplet ?>" width="130">
              </div>
              <h4 class="name mt-3"><?= Yii::$app->user->identity->nomComplet ?></h4>
            </div>
            <nav class="navbar-sidebar2">
                <ul class="list-unstyled navbar__list">
                    <?php if($user->esAdmin()): ?>
                      <li class="<?= $_SERVER['REQUEST_URI'] == '/users' ? 'active' : null ?> ">
                          <a href="/users">
                              <i class="fas fa-users"></i> Usuaris</a>
                      </li>
                      <li class="<?= $_SERVER['REQUEST_URI'] == '/noticies' ? 'active' : null ?> ">
                          <a href="/noticies">
                              <i class="fas fa-bullhorn"></i> Notícies de poble</a>
                      </li>
                    <?php endif; ?>
                      <li class="<?= $_SERVER['REQUEST_URI'] == '/media' ? 'active' : null ?> ">
                          <a href="/media">
                              <i class="fas fa-images"></i> Imatges i documents</a>
                      </li>
                </ul>
            </nav>
        </div>
    </aside>
    <div class="page-container2">
      <!-- HEADER -->
      <header class="header-desktop2">
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="header-wrap2">
                        <div class="logo d-block d-lg-none">
                            <a href="/">
                                <img src="/images/logo.png"  width="140"  alt="CoolAdmin">
                            </a>
                        </div>
                        <div class="header-button2">
                            <div class="header-button-item mr-0 js-sidebar-btn d-lg-none">
                                <i class="zmdi zmdi-menu"></i>
                            </div>
                            <div class="header-button-item mr-0 js-sidebar-btn d-lg-block d-none">
                                <i class="zmdi zmdi-account-circle"></i>
                            </div>
                            <div class="setting-menu js-right-sidebar d-none d-lg-block">
                                <div class="account-dropdown__body">
                                    <div class="account-dropdown__item">
                                        <a href="/ajustos">
                                            <i class="zmdi zmdi-edit"></i>Modificar perfil</a>
                                    </div>
                                </div>
                                <div class="account-dropdown__body">
                                    <div class="account-dropdown__item">
                                        <a href="/logout" data-method="post" data-confirm="Estàs segur de voler sortir?">
                                            <i class="zmdi zmdi-account-o"></i>Sortir</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <aside class="menu-sidebar2 js-right-sidebar d-block d-lg-none">
                <div class="logo">
                    <a href="/">
                        <img src="/images/logo.png"  width="140" alt="Cool Admin">
                    </a>
                </div>
                <div class="menu-sidebar2__content js-scrollbar2">
                    <div class="account2">
                      <div class="img-cir">
                        <img class="p-3" src="" alt="<?= $user->nomComplet ?>" width="130">
                      </div>
                      <span class="btn btn-info btn-sm my-2" >vblogv</span>
                      <h4 class="name mt-2"><?= Yii::$app->user->identity->nomComplet ?></h4>
                    </div>
                    <nav class="navbar-sidebar2">
                        <ul class="list-unstyled navbar__list">
                          <li class="has-sub">
                                <a class="js-arrow open" href="#">
                                    <i class="fas fa-user"></i> El teu compte
                                    <span class="arrow up">
                                        <i class="fas fa-angle-down"></i>
                                    </span>
                                </a>
                                <ul class="list-unstyled navbar__sub-list js-sub-list">
                                    <li>
                                        <a href="/ajustos">
                                            <i class="fas fa-tachometer-alt"></i>Modificar</a>
                                    </li>
                                    <!-- <li>
                                        <a href="/canviar-la-contrassenya">
                                            <i class="fas fa-lock"></i>Canvi de contra.</a>
                                    </li> -->
                                    <li>
                                        <a href="/desconectar" data-method="post" data-confirm="Estàs segur de voler sortir?">
                                            <i class="fas fa-power-off"></i>Desconectar</a>
                                    </li>
                                </ul>
                            </li>
                          <?php if($user->esAdmin()): ?>
                            <li class="<?= $_SERVER['REQUEST_URI'] == '/usuaris' ? 'active' : null ?> ">
                                <a href="/usuaris">
                                    <i class="fas fa-users"></i> Usuaris</a>
                            </li>
                            <li class="<?= $_SERVER['REQUEST_URI'] == '/noticies' ? 'active' : null ?> ">
                                <a href="/noticies">
                                    <i class="fas fa-bullhorn"></i> Notícies de poble</a>
                            </li>
                          <?php endif; ?>
                            <li class="<?= $_SERVER['REQUEST_URI'] == '/imatges' ? 'active' : null ?> ">
                                <a href="/imatges">
                                    <i class="fas fa-images"></i> Imatges i documents</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>
          <!--  CONTENT -->
            <section class="au-breadcrumb <?= (isset($this->params['breadcrumbs'])) ? 'm-t-75' : null ?>">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="au-breadcrumb-content">
                                    <div class="au-breadcrumb-left">
                                      <?php if(isset($this->params['breadcrumbs'])): ?>
                                        <span class="au-breadcrumb-span">Estas aquí:</span>
                                          <?= Breadcrumbs::widget([
                                              'homeLink' => ['label' => 'Inici', 'url' => '/'],
                                              'options' => ['class' => 'list-unstyled list-inline au-breadcrumb__list'],
                                              'itemTemplate' => "<li class=\"list-inline-item old\">{link}</li>\n",
                                              'links' => $this->params['breadcrumbs'],
                                              'activeItemTemplate' => "<li class=\"list-inline-item active\">{link}</li>\n"
                                          ]) ?>
                                      <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="section__content--p30 px-2 pt-4">
              <div class="container-fluid">
                <?php if(Yii::$app->session->hasFlash('general')): ?>
                  <div class="sufee-alert alert with-close alert-success alert-dismissible fade show">
                   <?= Yii::$app->session->getFlash('general'); ?>
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                  </div>
                <?php endif; ?>
                <?= $content ?>
              </div>
           </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
