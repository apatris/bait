<?php

/* @var $this yii\web\View */
/* @var $dataProvider */

use kartik\growl\Growl;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Proposals');
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-4 center-block" style="float: none;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'columns' => [
                    [
                        'attribute' => 'email',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->person->email;
                        }
                    ],
                    [
                        'attribute' => 'additionTime',
                        'format' => 'text',
                        'value' => function ($model) {
                            return date('d/m/Y H:i', strtotime($model->additionTime));
                        }
                    ],
                    [
                        'attribute' => '',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-ok text-success"></span>',
                                Url::to(['/proposals/activate/'.$model->id]),
                                [
                                    'data-method' => 'post',
                                    'data-pjax' => 0
                                ]
                            );
                        }
                    ],
                    [
                        'attribute' => '',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-remove text-danger"></span>',
                                Url::to(['/proposals/delete/'.$model->id]),
                                [
                                    'data-confirm' => 'Are you sure you want to delete this item?',
                                    'data-method' => 'post',
                                    'data-pjax' => 0
                                ]
                            );
                        }
                    ]
                ],
            ]) ?>
        </div>
    </div>

    <?= Yii::$app->session->hasFlash('add-user') ?
        Growl::widget([
            'type' => Yii::$app->session->getFlash('add-user')[0],
            'body' => Yii::$app->session->getFlash('add-user')[1],
            'pluginOptions' => [
                'showProgressbar' => true,
                'placement' => [
                    'from' => 'bottom',
                    'align' => 'right',
                ]
            ]
        ])
        :
        ''
    ?>
</div>
