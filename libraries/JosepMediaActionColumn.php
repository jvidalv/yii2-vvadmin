<?php

namespace app\libraries;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @author Josep Vidal
 */
class JosepMediaActionColumn extends \yii\grid\ActionColumn
{
    public $template = '<div class="table-data-feature">{update}{descarregar}{delete}</div>';

    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecordInterface $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
        }

        $params = is_array($key) ? $key : ['id' => (string)$key, 'slug' => $model->slug];
        $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

        return Url::toRoute($params);
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('update', 'pencil', ['data-modificar' => true]);
        $this->initDefaultButton('descarregar', 'download', ['data-descarregar' => true]);
        $this->initDefaultButton('delete', 'trash', [
            'data-confirm' => Yii::t('yii', 'Estas segur que vols esborrar-ho?'),
            'data-method' => 'post',
        ]);
    }

    /**
     * Initializes the default button rendering callback for single button.
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     * @since 2.0.11
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'descarregar':
                        $title = Yii::t('yii', 'Download');
                        $icon = 'zmdi zmdi-download';
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        $icon = 'zmdi zmdi-edit';
                        break;
                    case 'delete':
                            $title = Yii::t('yii', 'Delete');
                            $icon = 'zmdi zmdi-delete';
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'data-media-id' => $model->id,
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                    'class' => 'item',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('i', '', ['class' => $icon]);
                return Html::a($icon, $url, $options);
            };
        }
    }

}
