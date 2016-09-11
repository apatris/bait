<?php

namespace app\controllers;

use app\components\helpers\DynamicFormHelper;
use app\models\DynamicForm;
use app\models\Person;
use Yii;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class FormController extends Controller
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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'save'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['changeForms']
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'xml' => file_get_contents('../files/xml/form.xml')
        ]);
    }
    
    public function actionView()
    {
        $this->layout = 'proposal';

        return $this->render('view', [
            'xml' => file_get_contents('../files/xml/form.xml')
        ]);
    }

    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $return = false;

        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();

            if(!empty($post['xml'])){
                $return[] = file_put_contents('../files/xml/form.xml', $post['xml']);

                $form = new DynamicForm();
                $form->id = 0;
                $form->data = DynamicFormHelper::convertXMLToJSON($post['xml']);

                $return[] = $form->save();
            }
        }

        return $return;
    }
}
