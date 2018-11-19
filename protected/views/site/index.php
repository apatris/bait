<?php

/* @var $this yii\web\View */
/* @var $dataProvider */

use app\models\Person;
use kartik\growl\Growl;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Main page';
?>
<div class="site-index">

    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-white border-top-blue">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('app', 'Invite a person') ?></h3>
                </div>
                <div class="panel-footer">
                    <form action="<?= Url::to(['/email/send-invite']) ?>" method="post">
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
                        <div class="input-group">
                            <input required type="email" name="email" class="form-control" placeholder="<?= Yii::t('app', 'Enter email...') ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-success" type="submit"><?= Yii::t('app', 'Invite') ?></button>
                        </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="panel panel-white border-top-blue">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('app', 'The people you have invited') ?></h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => false,
                        'columns' => [
                            'email',
                            [
                                'attribute' => 'creationDate',
                                'format' => 'text',
                                'value' => function ($model) {
                                    return date('d/m/Y H:i', strtotime($model->creationDate));
                                }
                            ],
                            [
                                'attribute' => 'Status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $status = 0;

                                    if(is_object($model->proposal)){
                                        $status = $model->proposal->activated;
                                    }

                                    return '<b class="text-' . ($status ? 'success' : 'danger') . '">' . ($status ? 'active' : 'inactive') . '</b>';
                                }
                            ],
                            [
                                'attribute' => 'Resend invite',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a('resend',
                                        ['/email/resend-invite'],
                                        [
                                            'class' => 'btn btn-primary',
                                            'data' => [
                                                'method' => 'post',
                                                'params' => ['email' => $model->email]
                                            ],
                                        ]
                                    );;
                                }
                            ]
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <?= Yii::$app->session->hasFlash('send-invite') ?
        Growl::widget([
            'type' => Yii::$app->session->getFlash('send-invite')[0],
            'body' => Yii::$app->session->getFlash('send-invite')[1],
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
