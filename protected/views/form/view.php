<?php

/* @var $this yii\web\View */
use app\components\widgets\FormWidget;

/* @var $xml */

$this->title = Yii::t('app', 'View dynamic form');
?>
<div class="register pace-done" style="background: none; padding-top: 0">
    <div class="row container">
        <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
            <div class="form-container">
                <div class="bottom-wrapper">
                    <?= FormWidget::widget([
                        'xml' => $xml
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
