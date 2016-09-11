<?php

namespace app\components\managers;


use app\models\Person;

class TreeManager
{
    public static function getCode($id){
        $list = Person::getNestedList();

        $code = self::getAll($list, $id);

        return $code;
    }

    public static function getAll($list, $element, $code = ''){
        $index = self::getIndex($list, $element);

        if(empty($code)){
            $code = $index;
        }else{
            $code = $index . '_' . $code;
        }

        $parent = self::getParent($list, $element);

        if(!empty($parent)){
            $code = self::getAll($list, $parent, $code);
        }

        return $code;
    }

    public static function getParent($list, $id){

        foreach ($list as $parent => $childs){
            foreach ($childs as $childId => $childEmail) {
                if($childId == $id){
                    return $parent;
                }
            }
        }

        return 0;
    }

    public static function getIndex($list, $id){

        foreach ($list as $parent => $childs){
            $childIndex = 1;

            foreach ($childs as $childId => $childEmail) {
                if($childId == $id){
                    return $childIndex;
                }

                ++$childIndex;
            }
        }

        return 0;
    }
}