<?php

/* @var $this yii\web\View */
/* @var $dataProvider */
/* @var $email */

use app\components\widgets\FormWidget;

$this->title = Yii::t('app', 'Request is successfully sent');
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-6 center-block" style="float: none;">
            <h3 class="text-center"><?= Yii::t('app', 'Congratulations') ?>!</h3>

            <p><?= Yii::t('app', 'Request is successfully sent') ?>.</p>
            <p><?= Yii::t('app', 'A letter with further instructions will be sent by email') ?> - <b><?= $email ?></b></p>
        </div>
    </div>
</div>
