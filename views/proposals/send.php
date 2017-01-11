<?php

/* @var $this yii\web\View */
/* @var $dataProvider */
/* @var $hash */

use app\components\widgets\FormWidget;

$this->title = Yii::t('app', 'Send own proposal');
?>

<div class="register pace-done" style="background: none; padding-top: 0">
    <div class="row container">
        <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
            <div class="form-container">
                <div class="bottom-wrapper">
                    <?= FormWidget::widget([
                        'hash' => $hash,
                        'settings' => [
                            'action' => \yii\helpers\Url::to(['save'])
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
