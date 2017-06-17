<?php

/* @var $this yii\web\View */
/* @var $person \app\models\Person */

use kartik\growl\Growl;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'User profile');

var_dump($person);
die();
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-6 center-block" style="float: none;">
            <div class="panel panel-white border-top-green">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('user', 'User Info') ?></h3>
                </div>
                <div class="panel-body">
                    <?php /*foreach ($person->data as $name => $value) {?>
                        <div class="body-section">
                            <h5 class="section-heading"><?= $name ?></h5>
                            <p class="section-content"><?= $value ?></p>
                        </div>
                    <?php }*/ ?>
                </div>
            </div>
        </div>
    </div>
</div>
