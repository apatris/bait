<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class FormBuilderAsset
 * @package app\assets
 */
class FormBuilderAsset extends AssetBundle
{
    public $css = [
        'css/form-builder.min.css',
        'css/form-render.min.css',
    ];
    public $js = [
        'js/form-builder.min.js',
        'js/form-render.min.js',
    ];
}
