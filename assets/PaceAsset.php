<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Pace will automatically monitor your ajax requests, event loop lag,
 * document ready state, and elements on your page to decide the progress.
 * On ajax navigation it will begin again!
 *
 * @see http://github.hubspot.com/pace/
 */
class PaceAsset extends AssetBundle
{
    public $sourcePath = '@bower/pace';
    public $css = [
        'themes/blue/pace-theme-flash.css',
    ];
    public $js = [
        'pace.min.js',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
    ];
}