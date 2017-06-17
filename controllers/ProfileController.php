<?php

namespace app\controllers;

use app\models\Person;
use app\models\Proposal;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

/**
 * Class ProfileController
 * @package app\controllers
 */
class ProfileController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ]
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $id = Yii::$app->request->get('id');

        if (!empty($id)) {
            $person = Person::findOne($id)->proposal;
        } else {
            $person = Person::findOne([
                'email' => Yii::$app->user->identity->email
            ])->proposal;
        }

        return $this->render('profile', [
            'person' => $person
        ]);

    }

}
