<?php

namespace app\models;

use app\components\managers\TreeManager;
use yii;
use yii\helpers\Url;

/**
 * This is the model class for table "person".
 *
 * @property integer $id
 * @property integer $whoInvited
 * @property integer $userId
 * @property string $code
 * @property string $email
 * @property string $inviteHash
 * @property string $creationDate
 * @property Proposal $proposal
 */
class Person extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['whoInvited', 'userId'], 'integer'],
            [['creationDate'], 'safe'],
            [['inviteHash', 'code'], 'string'],
            [['email'], 'email'],
            [['email', 'whoInvited'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('person', 'ID'),
            'whoInvited' => Yii::t('person', 'Who invited'),
            'userId' => Yii::t('person', 'User ID'),
            'code' => Yii::t('person', 'Email'),
            'email' => Yii::t('person', 'Email'),
            'inviteHash' => Yii::t('person', 'Invite hash'),
            'creationDate' => Yii::t('person', 'Creation date')
        ];
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateInviteHash(){
        $hash = preg_replace(
            '/[^a-zA-Z0-9]/ui',
            '',
            Yii::$app->getSecurity()->generatePasswordHash($this->email . $this->whoInvited)
        );

        return $hash;
    }

    public function sendInvite(){
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Want or no?')
            ->setHtmlBody('<a href="'.Url::to(['proposals/send/'.$this->inviteHash], true).'">Dołączać</a>')
            ->send();
    }

    /**
     * @param string $hash
     * @return bool
     */
    public static function issetHash($hash){
        $person = self::find()
            ->select(['id'])
            ->where(['inviteHash' => $hash])
            ->asArray()
            ->one();
        
        return !empty($person['id']) ? true : false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProposal(){
        return $this->hasOne(Proposal::className(), ['personId' => 'id']);
    }
    
    public static function getNestedList(){
        $allItems = self::find()->asArray()->all();
        $arr = [];

        foreach ($allItems as $item){
            $arr[$item['whoInvited']][$item['userId']] = $item['email'];
        }
        
        return $arr;
    }

    public static function getChilds($user){
        $allItems = self::find()->asArray()->all();
        $arr = [];

        foreach ($allItems as $item){
            if (!empty($item['userId'])) {
                $arr[$item['whoInvited']][$item['userId']] = $item;
            }
        }

        $new = self::getRelatedItems($arr, $user->id);

        $new[0] = [
            $user->id => [
                'code' => '0',
                'email' => Yii::t('app', 'You') . ' (' . $user->email . ')'
            ]
        ];
        
        return $new;
    }

    private static function getRelatedItems($arr, $parent, $newArr = []){

        if(empty($arr[$parent])){
            return $newArr;
        }

        if(is_array($arr[$parent])){
            foreach ($arr[$parent] as $id => $item) {
                $newArr[$parent][$id] = $item;
                $newArr = self::getRelatedItems($arr, $id, $newArr);
            }
        }

        return $newArr;
    }
}
