<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\libraries\JosepGridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'articles');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="welcome p-t-10">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="title-4"><?= Html::encode($this->title) ?>
                </h1>
                <hr class="line-seprate">
            </div>
        </div>
    </div>
</section>
<section class="pt-5">
    <div class="container">
        <div class="article-index">
            <div class="table-data__tool">
                <div class="table-data__tool-left">
                </div>
                <div class="table-data__tool-right">
                    <form>
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal">
                                    + <?= Yii::t('app','create article') ?>
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fa fa-search"></i> <?= Yii::t('app', 'Filtrar') ?>
                                </button>
                            </div>
                            <input type="text" name="MediaSearch[general]" value="<?= $searchModel['general'] ?>"
                                onblur="insertParam('MediaSearch[general]', this.value  )"
                                placeholder="<?= Yii::t('app', '...') ?>" class="form-control">
                        </div>
                    </form>
                </div>
            </div>
            <?php Pjax::begin(); ?>
            <?= JosepGridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => 'app\libraries\JosepMediaActionColumn',
                            'contentOptions' => ['style' => 'width:5%;'],
                          ],
                        'id',
                        'language_id',
                        'user_id',
                        'media_id',
                        'category_id',
                    ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</section>