<?php

namespace app\components\helpers;


class DynamicFormHelper
{
    public static function convertXMLToJSON($xml){
        $form = simplexml_load_string($xml);
        $json = [];

        foreach ($form->fields->field as $field){
            $name = (string) $field->attributes()->name;
            if(!empty($name)){
                $json[$name] = '';
            }
        }
        
        return json_encode($json);
    }
}