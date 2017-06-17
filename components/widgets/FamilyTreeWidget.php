<?php

namespace app\components\widgets;

use yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

/**
 * Class FamilyTreeWidget
 * @package app\components\widgets
 */
class FamilyTreeWidget extends Widget
{
    public $nodes;
    public $wrapClass = 'block-center';
    public $wrapTreeClass = 'tree';

    public function init()
    {
        parent::init();
        
        if (empty($this->nodes)) {
            $this->nodes = [];
        }
    }

    public function run()
    {
        $html = '';

        if(!empty($this->nodes)){
            $html = Html::tag(
                'div',
                Html::tag(
                    'div',
                    $this->buildList($this->nodes[0]),
                    [
                        'class' => $this->wrapTreeClass
                    ]
                ),
                [
                    'class' => $this->wrapClass
                ]
            );
        }

        $this->getView()->registerCssFile('/css/family-tree.css');

        return $html;
    }

    private function buildList($nodes){
        $html = '';

        if(!empty($nodes) && is_array($nodes)){
            $html = Html::beginTag('ul');

            foreach ($nodes as $id => $item) {
                $html .= Html::tag(
                    'li',
                    $this->buildListItem($id, $item)
                );
            }

            $html .= Html::endTag('ul');
        }

        return $html;
    }

    protected function buildListItem($id, $item)
    {
        $html = '';

        if(!empty($item['code'])){
            $html .= Html::tag('a', $item['email'] . ' [' . $item['code'] . ']');
        }else{
            $html .= Html::tag('a', $item['email']);
        }


        if (key_exists($id, $this->nodes)) {
            $html .= $this->buildList($this->nodes[$id]);
        }

        return $html;
    }
}