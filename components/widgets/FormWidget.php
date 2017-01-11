<?php

namespace app\components\widgets;

use yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class FormWidget extends Widget
{
    public $xml;
    public $settings;
    public $hash;

    public function init()
    {
        parent::init();
        
        if (empty($this->xml)) {
            $this->xml = simplexml_load_file('../files/xml/form.xml');
        } else {
            $this->xml = simplexml_load_string($this->xml);
        }
        
        if (empty($this->settings['action'])) {
            $this->settings['action'] = '/';
        }

        if (empty($this->settings['method'])) {
            $this->settings['method'] = 'POST';
        }

        if (empty($this->settings['class'])) {
            $this->settings['class'] = 'login-form';
        }
    }

    public function run()
    {
        $html = '';
        
        if(!empty($this->xml)){
            $html .= '<form action="' . $this->settings['action'] . '" class="' . $this->settings['class'] . '" method="' . $this->settings['method'] . '">';

            $html .= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
            
            foreach ($this->xml->fields->field as $field){
                $html .= $this->generateField($field);
            }

            if(!empty($this->hash)){
                $html .= Html::hiddenInput('inviteHash', $this->hash);
            }

            $html .= '</form>';
        }

        return $html;
    }

    private function generateField($field){
        $fieldHtml = '';

        switch ($field['type']){
            case 'select':
                $fieldHtml .= $this->generateLabel($field);
                $fieldHtml .= $this->generateList(
                    $field,
                    'dropdownList',
                    [
                        'class' => 'form-control'
                    ]
                );
                break;
            case 'radio-group':
                $fieldHtml .= $this->generateLabel($field);
                $fieldHtml .= $this->generateList(
                    $field,
                    'radioList',
                    [
                        'class' => '',
                        'separator' => '<br>'
                    ]
                );
                break;
            case 'checkbox-group':
                $fieldHtml .= $this->generateLabel($field);
                $fieldHtml .= $this->generateList($field, 'checkboxList');
                break;
            case 'text':
                $options = [
                    'class' => $field['class'],
                ];

                if(!empty($field['required'])){
                    $options['required'] = 'required';
                }

                $fieldHtml .= $this->generateLabel($field);
                $fieldHtml .= Html::textInput(
                    $field['name'],
                    '',
                    $options
                );
                break;
            case 'textarea':
                $options = [
                    'class' => $field['class'],
                ];

                if(!empty($field['required'])){
                    $options['required'] = 'required';
                }

                $fieldHtml .= $this->generateLabel($field);
                $fieldHtml .= Html::textarea(
                    $field['name'],
                    '',
                    $options
                );
                break;
            case 'checkbox':
                $fieldHtml .= $this->generateLabel($field);
                $fieldHtml .= Html::checkbox(
                    $field['name'],
                    '',
                    [
                        'class' => $field['class']
                    ]
                );
                break;
            case 'header':
            case 'paragraph':
                $fieldHtml .= Html::tag(
                    $field['subtype'],
                    $field['label'],
                    [
                        'class' => $field['class'],
                    ]
                );
                break;
            case 'date':
                $options = [
                    'class' => $field['class'],
                ];

                if(!empty($field['required'])){
                    $options['required'] = 'required';
                }

                $fieldHtml .= $this->generateLabel($field);
                $fieldHtml .= Html::input(
                    $field['type'],
                    $field['name'],
                    null,
                    $options
                );
                break;
            case 'button':
                $fieldHtml .= Html::button(
                    $field['label'],
                    [
                        'class' => $field['class'],
                        'type' => 'submit'
                    ]
                );
                break;
        }

        $html = Html::tag(
            'div',
            $fieldHtml,
            [
                'class' => 'form-group'
            ]
        );

        return $html;
    }

    /**
     * @param \SimpleXMLElement $field
     * @return array
     */
    private function generateOptions(\SimpleXMLElement $field){
        $arr = [];

        foreach ($field as $option){
            $arr[(string) $option->attributes()[0]] = $option[0];
        }

        return $arr;
    }

    /**
     * @param \SimpleXMLElement $field
     * @return string
     */
    private function generateLabel(\SimpleXMLElement $field, $options = []){
        if(empty($options)){
            $options = [
                'class' => 'control-label'
            ];
        }

        if(!empty($field['required'])){
            $field['label'] .= ' *';
        }

        return Html::label(
            $field['label'],
            $field['name'],
            $options
        );
    }

    /**
     * @param \SimpleXMLElement $field
     * @param $type
     * @param array $options
     * @return mixed
     */
    private function generateList(\SimpleXMLElement $field, $type, $options = []){
        if(empty($options)){
            $options = [
                'class' => $field['class'].' checkbox',
                'separator' => '<br>'
            ];
        }

        return Html::$type(
            $field['name'],
            null,
            $this->generateOptions($field),
            $options
        );
    }

    public static function fillFields($fields){
        $js = '';
        
        foreach ($fields as $name => $value) {
            if (is_array($value)) {
                $value = Html::encode($value[0]);
            } else {
                $value = Html::encode($value);
            }

            $js .= "$('[name={$name}]').val('{$value}');";
        }
        
        return $js;
    }
}