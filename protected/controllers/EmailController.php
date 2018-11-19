<?php

namespace app\controllers;

use app\models\Person;
use dektrium\user\models\User;
use kartik\growl\Growl;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class EmailController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'send-invite'  => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ]
            ]
        ];
    }

    public function actionSendInvite ()
    {
        $post = Yii::$app->request->post();
        
        if (!empty($post['email'])) {
            if (!empty(Person::findOne(['email' => $post['email']])) || !empty(User::findOne(['email' => $post['email']]))) {
                Yii::$app->session->addFlash('send-invite', Growl::TYPE_DANGER);
                Yii::$app->session->addFlash('send-invite', 'This email already exists!');
            } else {
                $person = new Person();
                $person->id = 0;
                $person->whoInvited = Yii::$app->user->id;
                $person->email = $post['email'];
                $person->inviteHash = $person->generateInviteHash();

                if($person->save()){
                    $person->sendInvite();

                    Yii::$app->session->addFlash('send-invite', Growl::TYPE_SUCCESS);
                    Yii::$app->session->addFlash('send-invite', 'The invitation was sent successfully!');
                }else{
                    Yii::$app->session->addFlash('send-invite', Growl::TYPE_DANGER);
                    Yii::$app->session->addFlash('send-invite', 'E-mail is incorrect!');
                }
            }
        }
        
        return $this->redirect('/');
    }

    public function actionResendInvite ()
    {
        $post = Yii::$app->request->post();

        if (!empty($post['email'])) {
            $person = Person::findOne(['email' => $post['email']]);
            if (!empty($person)) {
                Yii::trace($person->sendInvite());

                Yii::$app->session->addFlash('send-invite', Growl::TYPE_SUCCESS);
                Yii::$app->session->addFlash('send-invite', 'The invitation was resent successfully!');
            }
        }

        return $this->redirect('/');
    }
}
