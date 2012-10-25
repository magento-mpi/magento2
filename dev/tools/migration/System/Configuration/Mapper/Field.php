<?php

class Tools_Migration_System_Configuration_Mapper_Field extends Tools_Migration_System_Configuration_Mapper_Abstract
{
    public function __construct()
    {
        parent::__construct();

        $this->_allowedFieldNames = array(
            'label',
            'comment',
            'tooltip',
            'frontend_class',
            'validate',
            'can_be_empty',
            'if_module_enabled',
            'frontend_model',
            'backend_model',
            'source_model',
            'config_path',
            'base_url',
            'upload_dir',
            'button_url',
            'button_label',
            'depends',
            'more_url',
            'demo_url',
        );
    }

    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $fieldName => $fieldConfig) {
            $output[] = $this->_transformElement($fieldName, $fieldConfig, 'field', $this->_allowedFieldNames);
        }
        return $output;
    }

    public function _transformSubConfig(array $config, $parentNode, $element)
    {
        switch ($parentNode['name']) {
            case 'depends':
                $parentNode['subConfig'] = $this->_transformElementDepends($config);
                break;

            case 'attribute':
                $parentNode['subConfig'] = $this->_transformElementAttribute($config);
                break;

        }
        $element['parameters'][] = $parentNode;

        return $element;
    }

    protected function _transformElementDepends(array $config)
    {
        $result = array();
        foreach ($config as $nodeName => $nodeValue) {
            $element = array();
            $element['nodeName'] = 'field';
            $element['@attributes']['id'] = $nodeName;
            $attributes = isset($nodeValue['@attributes']) ? $nodeValue['@attributes'] : array();
            $element = $this->_transformAttributes($attributes, $element);

            if (false === empty($attributes)) {
                unset($nodeValue['@attributes']);
            }

            $element['#text'] = $nodeValue['#text'];
            $result[] = $element;
        }

        return $result;
    }

    protected function _transformElementAttribute(array $config)
    {
        $result = array();
        foreach ($config as $nodeName => $nodeValue) {
            $element = array();
            $element['nodeName'] = $nodeName;
            $attributes = isset($nodeValue['@attributes']) ? $nodeValue['@attributes'] : array();
            $element = $this->_transformAttributes($attributes, $element);

            if (false === empty($attributes)) {
                unset($nodeValue['@attributes']);
            }
            if (is_array($nodeValue) && !(isset($nodeValue['#text']) || isset($nodeValue['#cdata-section']))) {
                $element['subConfig'] = $this->_transformElementAttribute($nodeValue);
            } else {
                if (isset($nodeValue['#text'])) {
                    $element['#text'] = $nodeValue['#text'];
                }
                if (isset($nodeValue['#cdata-section'])) {
                    $element['#cdata-section'] = $nodeValue['#cdata-section'];
                }
            }

            $result[] = $element;
        }

        return $result;
    }
}
