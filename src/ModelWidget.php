<?php
/**
 * @author Soshnikov Artem <213036@skobka.com>
 * @copyright (c) 22.06.2016 9:35
 */

namespace skobka\yii2\modelWidget;

use kartik\select2\Select2;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use yii\widgets\InputWidget;

/**
 * Class ModelWidget
 * @package skobka\yii2\ModelWidget
 *
 * @property ActiveRecord $model
 */
class ModelWidget extends InputWidget
{
    public $titleAttr;
    public $data;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (!$this->model instanceof ActiveRecord) {
            throw new \RuntimeException('Only ' . ActiveRecord::class . ' subclasses allowed');
        }

        if (!$this->titleAttr) {
            throw new \RuntimeException('You must specify titleAttr option');
        }
        parent::init();
    }


    /**
     * @inheritDoc
     */
    public function run()
    {
        echo Select2::widget([
            'model' => $this->model,
            'attribute' => $this->attribute,
            'data' => $this->getDataOptions(),
        ]);
    }

    /**
     * @return array
     */
    public function getDataOptions()
    {
        if ($this->data) {
            return $this->data;
        }

        $rel = $this->model->getRelation($this->attribute);
        /* @var $modelClass ActiveRecord */
        $modelClass = $rel->modelClass;
        $options = BaseArrayHelper::map($modelClass::find()->all(), $this->model->primaryKey(), $this->titleAttr);

        return $options;
    }
}
