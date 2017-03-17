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
        $targetAttribute = $this->getTargetAttribute();

        echo Select2::widget([
            'model' => $this->model,
            'attribute' => $targetAttribute,
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

        $modelClass = $this->getTargetModelClass();
        $options = BaseArrayHelper::map($modelClass::find()->all(), $this->getTargetPrimaryKey(), $this->titleAttr);

        return $options;
    }

    /**
     * @return string|ActiveRecord
     */
    protected function getTargetModelClass()
    {
        return $this->getRelation()->modelClass;
    }

    /**
     * @return \yii\db\ActiveQuery|\yii\db\ActiveQueryInterface
     */
    protected function getRelation()
    {
        $relation = $this->model->getRelation($this->attribute);

        return $relation;
    }

    /**
     * @return string
     */
    protected function getTargetAttribute()
    {
        $pk = $this->getTargetPrimaryKey();
        $relation = $this->getRelation();
        return $relation->link[$pk];
    }

    /**
     * @return string
     */
    protected function getTargetPrimaryKey()
    {
        $modelClass = $this->getTargetModelClass();
        $primaryKeys = $modelClass::primaryKey();

        return reset($primaryKeys);
    }
}
