<?php

/* @var $this yii\web\View */
use yii\web\View;
use app\assets\FormBuilderAsset;

/* @var $dataProvider */
/* @var $xml */

$js = '
    var fbTemplate = document.getElementById(\'fb-template\');
    $(fbTemplate).formBuilder();
    $(".form-builder-save").on("click", function(){
        $.ajax({
          url: "'.\yii\helpers\Url::to(['save']).'",
          type: "post",
          data: {
            xml: $("#fb-template").val()
          }
        });
    });
';

$this->registerJs($js, View::POS_READY);

FormBuilderAsset::register($this);

$this->title = Yii::t('app', 'Change dynamic form');
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-8 center-block" style="float: none;">
            <textarea id="fb-template"><?= $xml ?></textarea>
        </div>
    </div>
</div>
