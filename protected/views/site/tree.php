<?php

/* @var $this yii\web\View */
/* @var $nodes */

use app\components\widgets\FamilyTreeWidget;

$this->title = Yii::t('app', 'The people you have invited');
?>
<div class="site-invited-tree">

    <div class="row">
        <div class="col-lg-12 center-block" style="float: none;">
            
            <?= FamilyTreeWidget::widget([
                'nodes' => $nodes
            ])?>
        </div>
    </div>

</div>
