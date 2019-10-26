<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\libraries;


use Yii;
use yii\grid\GridView;

/**
 * @author Josep Vidal
 */
class JosepGridView extends GridView
{
  public $tableOptions = ['class' => 'table table-data2'];
  public $pager = ['class' => 'app\libraries\widgets\JosepLinkPager'];
  public $layout = "<div class=\"table-responsive table-responsive-data2\">\n{items}</div><div class=\"d-flex mt-3\">\n<div class=\"d-flex\">{summary}</div>\n<div class=\"ml-auto\">{pager}</div></div>";
}
