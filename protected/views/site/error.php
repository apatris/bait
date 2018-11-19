<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $name;
$this->context->layout = 'error';
?>

<div class="row">
    <div class="col-sm-12">
        <h1 class="title">404</h1>
    </div>
    <div class="col-sm-12">
        <p class="message"><?= nl2br(Html::encode($message)) ?></p>
    </div>
    <div class="col-sm-12">
        <p>
            <a href="<?= Url::to(['/']) ?>" class="btn btn-blue btn-lg"><?= Yii::t('app', 'Go Home') ?></a>
        </p>
    </div>
</div>
