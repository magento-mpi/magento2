<?php

abstract class Tools_Migration_System_Configuration_Mapper_Abstract
{
    /**
     * Attribute maps
     * oldName => newName
     * @var array
     */
    protected $_attributeMaps = array();

    /**
     * @var array
     */
    protected $_allowedFieldNames = array();

    public function __construct()
    {
        $this->_attributeMaps = array(
            'sort_order' => 'sortOrder',
            'show_in_default' => 'showInDefault',
            'show_in_store' => 'showInStore',
            'show_in_website' => 'showInWebsite',
            'frontend_type' => 'type',
        );

    }


    public abstract function transform(array $config);

    protected abstract function _transformSubConfig(array $config, $parentNode, $element);

    protected function _transformElement($nodeId, $config, $nodeName, $allowedNames = array())
    {
        $element = array();
        $element['nodeName'] = $nodeName;
        if (false === empty($nodeId)) {
            $element['@attributes']['id'] = $nodeId;
        }
        $attributes = isset($config['@attributes']) ? $config['@attributes'] : array();
        $element = $this->_transformAttributes($attributes, $element);

        if (false === empty($attributes)) {
            unset($config['@attributes']);
        }

        $element = $this->_transformNodes($config, $element, $allowedNames);
        return $element;
    }

    /**
     * Transform attributes
     *
     * @param array $attributes
     * @param array $element
     * @return array
     */
    protected function _transformAttributes($attributes, $element)
    {
        foreach ($attributes as $attributeName => $attributeValue) {
            $element['@attributes'][$this->_getAttributeName($attributeName)] = $attributeValue;
        }
        return $element;
    }

    /**
     * Get attribute name
     *
     * @param string $key
     * @return mixed
     */
    protected function _getAttributeName($key)
    {
        return isset($this->_attributeMaps[$key]) ? $this->_attributeMaps[$key] : $key;
    }

    protected function _needMoveToAttribute($key)
    {
        return isset($this->_attributeMaps[$key]);
    }

    protected function _transformNodes($config, $element, $allowedNames = array())
    {
        $element['parameters'] = array();
        foreach($config as $nodeName => $nodeValue) {
            if ($this->_needMoveToAttribute($nodeName)) {
                $element['@attributes'][$this->_getAttributeName($nodeName)] = $nodeValue['#text'];
                unset($config[$nodeName]);
                continue;
            }

            $node = array();
            if (false === empty($allowedNames) && false == in_array($nodeName, $allowedNames)) {
                $node['@attributes'] = array(
                    'type' => $nodeName
                );
                $nodeName = 'attribute';
            }

            $node['name'] = $nodeName;
            if (is_array($nodeValue) && !(isset($nodeValue['#text']) || isset($nodeValue['#cdata-section']))) {
                $element = $this->_transformSubConfig($nodeValue, $node, $element);
                continue;
            } else {
                if (isset($nodeValue['#text'])) {
                    $node['#text'] = $nodeValue['#text'];
                }
                if (isset($nodeValue['#cdata-section'])) {
                    $node['#cdata-section'] = $nodeValue['#cdata-section'];
                }
            }

            $element['parameters'][] = $node;
        }

        return $element;
    }

}
