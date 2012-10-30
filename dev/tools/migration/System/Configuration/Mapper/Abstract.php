<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

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


    /**
     * Transform configuration
     *
     * @param array $config
     * @return mixed
     */
    public abstract function transform(array $config);

    /**
     * Transform sub configuration
     *
     * @param array $config
     * @param array $parentNode
     * @param array $element
     * @return mixed
     */
    protected abstract function _transformSubConfig(array $config, $parentNode, $element);

    /**
     * Transform element configuration
     *
     * @param string $nodeId
     * @param array $config
     * @param string $nodeName
     * @param array $allowedNames
     * @return mixed
     */
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

    /**
     * Check if node must be moved to attribute
     *
     * @param string $key
     * @return bool
     */
    protected function _needMoveToAttribute($key)
    {
        return isset($this->_attributeMaps[$key]);
    }

    /**
     * Transform nodes configuration
     *
     * @param array $config
     * @param array $element
     * @param array $allowedNames
     * @return mixed
     */
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
                if (isset($nodeValue['@attributes'])) {
                    if (isset($node['@attributes'])) {
                        $node['@attributes'] = array_merge($node['@attributes'], $nodeValue['@attributes']);
                    } else {
                        $node['@attributes'] = $nodeValue['@attributes'];
                    }
                }

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
