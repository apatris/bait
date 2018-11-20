<?php

namespace app\controllers;

use yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use app\components\managers\TreeManager;
use dektrium\user\models\User;
use kartik\growl\Growl;
use app\models\Person;
use app\models\Proposal;

/**
 * Class ProposalsController
 * @package app\controllers
 */
class ProposalsController extends Controller
{
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

    /**
     * @return string
     */
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

    /**
     * @return string
     * @throws NotFoundHttpException
     */
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

    /**
     * @param $hash
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSend($hash)
    {
        $person = Person::findOne(['inviteHash' => $hash]);

        if (!empty($person->id) && !Proposal::issetBid($person->id)) {
            return $this->render('send', [
                'hash' => $hash
            ]);
        } else {
            throw new NotFoundHttpException('Page not found.');
        }
    }

    /**
     * @return yii\web\Response
     * @throws NotFoundHttpException
     */
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


    /**
     * @param $id
     * @return yii\web\Response
     * @throws \Exception
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $proposal = Proposal::findOne($id);

        if(!empty($proposal->id)){
            $proposal->delete();
        }

        return $this->redirect('/proposals');
    }

    /**
     * @param $id
     * @return yii\web\Response
     * @throws yii\base\InvalidConfigException
     */
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
