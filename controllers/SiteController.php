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

class SiteController extends Controller
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

    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $proposal = Proposal::findOne(Person::findOne(['userId' => Yii::$app->user->id])->proposal->id);

            if(!empty($proposal)){
                unset($post['_csrf']);

                $proposal->data = Json::encode($post);
                $proposal->additionTime = date('Y-m-d H:i:s');

                $proposal->save();
            }

            return $this->redirect(Url::to(['/']));
        }

        if (Proposal::isActiveChanges(Yii::$app->user->id)) {
            $this->layout = 'special';

            return $this->render('form', [
                'xml' => file_get_contents('../files/xml/form.xml'),
                'fields' => json_decode(
                    Person::findOne(['userId' => Yii::$app->user->id])->proposal->data,
                    true
                )
            ]);
        } else {
            $dataProvider =  new ActiveDataProvider([
                'query' => Person::find()->where(['whoInvited' => Yii::$app->user->id])->orderBy(['creationDate' => SORT_DESC]),
                'sort' => false
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider
            ]);
        }
    }

    public function actionInvitedTree()
    {
        return $this->render('tree', [
            'nodes' => Person::getChilds(Yii::$app->user->getIdentity())
        ]);
    }

    public function actionUserProfile()
    {

          $personId = Yii::$app->request->get('id');
          $person = new Person($personId);

            if ($person->proposal->data) {
                return $this->render('profile', [
                    'data' => Json::decode($person->proposal->data)
                ]);
            } else {
                throw new HttpNotFoundException();
            }

    }


}
