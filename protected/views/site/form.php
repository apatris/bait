<?php

/* @var $this yii\web\View */
use app\components\widgets\FormWidget;

/* @var $xml */
/* @var $fields */

$this->title = Yii::t('app', 'View dynamic form');

$this->registerJs(FormWidget::fillFields($fields));

?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-8 center-block" style="float: none;">
            <?= FormWidget::widget([
                'xml' => $xml
            ]) ?>
        </div>
    </div>
</div>
