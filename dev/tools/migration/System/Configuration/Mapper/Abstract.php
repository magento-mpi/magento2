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
    protected $_attributeMaps = array(
        'sort_order' => 'sortOrder',
        'show_in_default' => 'showInDefault',
        'show_in_store' => 'showInStore',
        'show_in_website' => 'showInWebsite',
        'frontend_type' => 'type',
    );

    /**
     * List of allowed field names
     *
     * @var array
     */
    protected $_allowedFieldNames = array();

    /**
     * Transform configuration
     *
     * @param array $config
     * @return array
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
     * @return array
     */
    protected function _transformElement($nodeId, $config, $nodeName, $allowedNames = array())
    {
        $element = array();
        $element['nodeName'] = $nodeName;
        if (false === empty($nodeId)) {
            $element['@attributes']['id'] = $nodeId;
        }
        $attributes =  $this->_getValue($config, '@attributes', array());
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
     * @return string
     */
    protected function _getAttributeName($key)
    {
        return $this->_getValue($this->_attributeMaps, $key, $key);
    }

    /**
     * Check if node must be moved to attribute
     *
     * @param string $key
     * @return bool
     */
    protected function _needMoveToAttribute($key)
    {
        return $this->_getValue($this->_attributeMaps, $key, false);
    }

    /**
     * Transform nodes configuration
     *
     * @param array $config
     * @param array $element
     * @param array $allowedNames
     * @return array
     */
    protected function _transformNodes($config, $element, $allowedNames = array())
    {
        $element['parameters'] = array();
        foreach ($config as $nodeName => $nodeValue) {
            if ($this->_needMoveToAttribute($nodeName)) {
                $element['@attributes'][$this->_getAttributeName($nodeName)] = $nodeValue['#text'];
                unset($config[$nodeName]);
                continue;
            }

            $node = array();
            if ($this->_isNotAllowedNodeName($allowedNames, $nodeName)) {
                $node['@attributes'] = array('type' => $nodeName);
                $nodeName = 'attribute';
            }

            $node['name'] = $nodeName;
            if ($this->_isSubConfigValue($nodeValue)) {
                $element = $this->_transformSubConfig($nodeValue, $node, $element);
                continue;
            } else {
                if ($this->_getValue($nodeValue, '@attributes', false)) {
                    if ($this->_getValue($node, '@attributes', false)) {
                        $node['@attributes'] = array_merge(
                            $this->_getValue($node, '@attributes', array()),
                            $this->_getValue($nodeValue, '@attributes', array())
                        );
                    } else {
                        $node['@attributes'] = $this->_getValue($nodeValue, '@attributes', array());
                    }
                }

                if ($this->_getValue($nodeValue, '#text', false)) {
                    $node['#text'] = $this->_getValue($nodeValue, '#text');
                }
                if ($this->_getValue($nodeValue, '#cdata-section', false)) {
                    $node['#cdata-section'] = $this->_getValue($nodeValue, '#cdata-section');
                }
            }

            $element['parameters'][] = $node;
        }

        return $element;
    }

    /**
     * Check if node value must be converted as sub config
     *
     * @param mixed $nodeValue
     * @return bool
     */
    protected function _isSubConfigValue($nodeValue)
    {
        return is_array($nodeValue) &&
            !($this->_getValue($nodeValue, '#text', false) || $this->_getValue($nodeValue, '#cdata-section', false));
    }

    /**
     * Check if specified node name is not allowed
     *
     * @param array $allowedNames
     * @param string $nodeName
     * @return bool
     */
    protected function _isNotAllowedNodeName($allowedNames, $nodeName)
    {
        return false === empty($allowedNames) && false == in_array($nodeName, $allowedNames);
    }

    /**
     * Get value from array by key
     *
     * @param array $source
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getValue($source, $key, $default = null)
    {
        return array_key_exists($key, $source) ? $source[$key] : $default;
    }
}
