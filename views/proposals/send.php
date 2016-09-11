<?php

/* @var $this yii\web\View */
/* @var $dataProvider */
/* @var $hash */

use app\components\widgets\FormWidget;

$this->title = Yii::t('app', 'Send own proposal');
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-4 center-block" style="float: none;">
            <?= FormWidget::widget([
                'hash' => $hash,
                'settings' => [
                    'action' => \yii\helpers\Url::to(['save']),
                    'method' => 'POST'
                ]
            ]) ?>
        </div>
    </div>
</div>
