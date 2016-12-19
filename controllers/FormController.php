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

/**
 * Class FormController
 * @package app\controllers
 */
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

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'xml' => file_get_contents('../files/xml/form.xml')
        ]);
    }

    /**
     * @return string
     */
    public function actionView()
    {
        return $this->render('view', [
            'xml' => file_get_contents('../files/xml/form.xml')
        ]);
    }

    /**
     * @return array|bool
     */
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
