<?php

namespace app\controllers;

use app\components\managers\TreeManager;
use dektrium\user\models\User;
use kartik\growl\Growl;
use yii;
use app\models\Person;
use app\models\Proposal;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class ProposalsController extends Controller
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
                    'save' => ['post'],
                    'delete' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['activateProfiles']
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $dataProvider =  new ActiveDataProvider([
            'query' => Proposal::find()->where(['activated' => 0]),
            'sort' => false
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionSuccessfully()
    {
        $person = Person::findOne(['inviteHash' => Yii::$app->request->get('hash')]);

        if (!empty($person->id) && Proposal::issetBid($person->id)) {
            return $this->render('successfully', [
                'email' => $person->email
            ]);
        } else {
            throw new NotFoundHttpException('Page not found.');
        }
    }

    public function actionSend()
    {
        $hash = Yii::$app->request->get('hash');

        $person = Person::findOne(['inviteHash' => $hash]);

        if (!empty($person->id) && !Proposal::issetBid($person->id)) {
            return $this->render('send', [
                'hash' => $hash
            ]);
        } else {
            throw new NotFoundHttpException('Page not found.');
        }
    }

    public function actionSave()
    {
        $post = Yii::$app->request->post();
        $inviteHash = $post['inviteHash'];

        unset($post['_csrf']);
        unset($post['inviteHash']);

        if (!empty($inviteHash)) {
            $person = Person::findOne(['inviteHash' => $inviteHash]);

            if (!empty($person->id) && !Proposal::issetBid($person->id)) {
                $proposal = new Proposal();

                $proposal->id = 0;
                $proposal->personId = $person->id;
                $proposal->data = Json::encode($post);

                if($proposal->save()){
                    return $this->redirect('successfully/' . $inviteHash);
                }
            }
        }

        throw new NotFoundHttpException('Your data is wrong.');
    }


    public function actionDelete($id)
    {
        $proposal = Proposal::findOne($id);

        if(!empty($proposal->id)){
            $proposal->delete();
        }

        return $this->redirect('/proposals');
    }

    public function actionActivate($id)
    {
        $proposal = Proposal::findOne($id);

        if(!empty($proposal->id)){
            $user = Yii::createObject([
                'class'    => User::className(),
                'scenario' => 'create',
                'email'    => $proposal->person->email,
                'username' => substr($proposal->person->email, 0, strpos($proposal->person->email, '@')),
                'password' => null,
            ]);

            if ($user->create()) {
                $proposal->activated = 1;
                $proposal->save();

                $person = Person::findOne($proposal->person->id);
                $person->userId = $user->id;

                if($person->save()){
                    $person->code = TreeManager::getCode($user->id);
                    $person->save();
                }
                
                Yii::$app->session->addFlash('add-user', Growl::TYPE_SUCCESS);
                Yii::$app->session->addFlash('add-user', 'User was created successfully!');
            }else{
                Yii::$app->session->addFlash('add-user', Growl::TYPE_DANGER);
                Yii::$app->session->addFlash('add-user', 'User can not created!');
            }

        }

        return $this->redirect('/proposals');
    }
}
