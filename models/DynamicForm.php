<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dynamic_form".
 *
 * @property integer $id
 * @property string $data
 * @property string $creationDate
 */
class DynamicForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dynamic_form';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data'], 'required'],
            [['data'], 'string'],
            [['creationDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data' => Yii::t('app', 'Data'),
            'creationDate' => Yii::t('app', 'Creation Date'),
        ];
    }
}
