<?php

/* @var $this yii\web\View */
use app\components\widgets\FormWidget;

/* @var $xml */

$this->title = Yii::t('app', 'View dynamic form');
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-4 center-block" style="float: none;">
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
