<?php

namespace app\components\widgets;

use yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;

class LanguageSwitcher
{
    public static function rrun()
    {
        $languages = isset(Yii::$app->getUrlManager()->languages) ? Yii::$app->getUrlManager()->languages : [];
        if (count($languages) > 1) {
            $items = [];
            $currentUrl = preg_replace('/' . Yii::$app->language . '\//', '', Yii::$app->getRequest()->getUrl(), 1);
            $isAssociative = ArrayHelper::isAssociative($languages);
            foreach ($languages as $language => $code) {
                $url = '/' . $code . $currentUrl;
                if ($isAssociative) {
                    $item = ['label' => $language, 'url' => $url];
                } else {
                    $item = ['label' => $code, 'url' => $url];
                }
                if ($code === Yii::$app->language) {
                    $item['options']['class'] = 'disabled';
                }
                $items[] = $item;
            }
            
            return $items;
        }
        
        return [];
    }

    public static function run(){
        $items = [];
        $langs = Yii::$app->getUrlManager()->languages;
        $label = '';

        if (!empty($langs)) {
            $items = [];
            $currentUrl = self::getLangUrl();
            $current = self::getUrlFromCode(Yii::$app->language);
            $isAssociative = ArrayHelper::isAssociative($langs);
            $currentIcon = $current;

            if($current == 'en'){
                $currentIcon = 'us';
            }

            foreach ($langs as $language => $code) {
                if($code == $current){
                    $label = $language;
                    continue;
                }

                $codeIcon = $code;

                if($codeIcon == 'en'){
                    $codeIcon = 'us';
                }

                $url = '/' . $code . $currentUrl;

                if ($isAssociative) {
                    $item = [
                        'label' => Html::img(
                            'https://lipis.github.io/flag-icon-css/flags/1x1/' . $codeIcon . '.svg',
                            [
                                'class' => 'flag-icon'
                            ]
                            ),
                        'url' => $url,
                        'encode'
                    ];
                } else {
                    $item = [
                        'label' => Html::img(
                            'https://lipis.github.io/flag-icon-css/flags/1x1/' . $codeIcon . '.svg',
                            [
                                'class' => 'flag-icon'
                            ]
                        ),
                        'url' => $url,
                        'encode'
                    ];
                }

                $items[] = $item;
            }
        }

        return [
            'label' => Html::img(
                'https://lipis.github.io/flag-icon-css/flags/1x1/' . $currentIcon . '.svg',
                [
                    'class' => 'flag-icon'
                ]
            ),
            'items' => $items
        ];
    }

    private static function getLangUrl(){
        $_lang_url = Yii::$app->getRequest()->getUrl();
        $current = self::getUrlFromCode(Yii::$app->language);

        $url_list = explode('/', $_lang_url);

        $lang_url = isset($url_list[1]) ? $url_list[1] : null;

        //Lang::setCurrent($lang_url);

        if( $lang_url !== null && $lang_url === $current &&
            strpos($_lang_url, $current) === 1 )
        {
            $_lang_url = substr($_lang_url, strlen($current)+1);
        }

        return $_lang_url;
    }

    private static function getUrlFromCode($code){
        list($url, $some) = explode('-', $code);

        return $url;
    }
}