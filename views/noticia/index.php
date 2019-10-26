<?php

use yii\helpers\Html;
use yii\grid\CheckboxColumn;
use yii\widgets\ActiveForm;
use app\libraries\JosepGridView;
use app\libraries\JosepDates;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NoticiaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Llistat Notícies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="noticia-index">
  <div class="card">
    <div class="card-header d-flex">
        <h1 class="title-1"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('+ Afegir notícia', ['create'], ['class' => 'au-btn au-btn-icon au-btn--green au-btn--small ml-auto']) ?>
    </div>
    <?php Pjax::begin(); ?>
    <div class="card-body">
      <div class="table-data__tool">
          <div class="table-data__tool-left">
            <ul class="nav nav-pills">
  						<li class="nav-item small">
  							<div class="nav-link cursor-pointer <?= $activa == 'totals' ? 'active' : null ?>" onclick="insertParam('NoticiaSearch[borrat]', 0)">Totals (<?= $tamanyActives ?>)</div>
  						</li>
  						<li class="nav-item cursor-pointer small">
                <div class="nav-link cursor-pointer <?= $activa == 'paperera' ? 'active' : null ?>" onclick="insertParam('NoticiaSearch[borrat]', 1)">Paperera (<?= $tamanyPapelera ?>)</div>
  						</li>
  					</ul>
          </div>
          <div class="table-data__tool-right">
            <form>
              <div class="input-group">
                  <div class="input-group-btn">
                      <button class="btn btn-primary">
                          <i class="fa fa-search"></i> <?= Yii::t('app', 'Filtrar') ?>
                      </button>
                  </div>
                  <input type="text" name="NoticiaSearch[general]" value="<?= $searchModel['general'] ?>" onblur="insertParam('NoticiaSearch[general]', this.value  )" placeholder="<?= Yii::t('app', '...') ?>" class="form-control">
              </div>
            </form>
          </div>
      </div>
      <?= JosepGridView::widget([
          'dataProvider' => $dataProvider,
          'columns' => [
              ['class' => 'app\libraries\JosepActionColumn'],
              [
                 'attribute' => 'titol',
                 'label' => 'Títol',
                 'format' => 'raw',
                 'value' => function ($model, $key, $index) {
                   return ( $model->titol ? $model->titol : '(BORRADOR)');
                 },
              ],
              [
                 'attribute' => 'data_publicacio',
                 'format' => 'raw',
                 'value' => function ($model, $key, $index) {
                     $string = JosepDates::esMesGran($model->data_publicacio) ? '<span class="status--process"><i class="fas fa-check-circle"></i> ' : '<span class="status--denied">';
                     return  $string . ( $model->data_publicacio ? $model->dataPublicacio : '(PENDENT)' ) . '</span>';
                 },
              ],
              [
               'attribute' => 'user_id',
               'label' => 'Usuari',
               'format' => 'raw',
               'value' => function ($model, $key, $index) {
                   return  $model->user->nomComplet;
               },
              ],
              //'capcalera',
              //'cos',
              //'imatge',
              //'slug',
              //'data_publicacio',
              //'updated_at',
              //'created_at',


          ],
      ]); ?>
    </div>
    <?php Pjax::end(); ?>
  </div>
</div>
