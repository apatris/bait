<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proposal".
 *
 * @property string $id
 * @property string $personId
 * @property string $data
 * @property string $additionTime
 * @property integer $activated
 *
 * @property Person $person
 */
class Proposal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proposal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['personId'], 'required'],
            [['personId', 'activated'], 'integer'],
            [['data', 'additionTime'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('proposal', 'ID'),
            'personId' => Yii::t('proposal', 'Person ID'),
            'data' => Yii::t('proposal', 'Data'),
            'additionTime' => Yii::t('proposal', 'Addition time'),
            'activated' => Yii::t('proposal', 'Status'),
        ];
    }


    public static function isActiveChanges($userId){
        $person = Person::findOne(['userId' => $userId]);

        if(!empty($person) && !empty($person->proposal)){
            $lastForm = DynamicForm::find()->orderBy(['creationDate' => SORT_DESC])->asArray()->one();
            $userForm = $person->proposal;

            if($lastForm['creationDate'] > $userForm['additionTime']){
                $diff = array_diff_key(
                    json_decode($lastForm['data'], true),
                    json_decode($userForm['data'], true)
                );

                if(!empty($diff)){
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * @return int
     */
    public static function getCount(){
        return self::find()->where(['activated' => 0])->count();
    }

    /**
     * @param integer $personId
     * @return bool
     */
    public static function issetBid($personId){
        $proposal = Proposal::findOne(['personId' => $personId]);
        return !empty($proposal->id) ? true : false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson(){
        return $this->hasOne(Person::className(), ['id' => 'personId']);
    }
}
