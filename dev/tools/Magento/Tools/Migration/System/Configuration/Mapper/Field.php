<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Migration\System\Configuration\Mapper;

class Field extends \Magento\Tools\Migration\System\Configuration\Mapper\AbstractMapper
{
    /**
     * List of allowed node names
     *
     * @var string[]
     */
    protected $_allowedFieldNames = array(
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
        'hide_in_single_store_mode'
    );

    /**
     * Transform field config
     *
     * @param array $config
     * @return array
     */
    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $fieldName => $fieldConfig) {
            $output[] = $this->_transformElement($fieldName, $fieldConfig, 'field', $this->_allowedFieldNames);
        }
        return $output;
    }

    /**
     * Transform sub configuration
     *
     * @param array $config
     * @param array $parentNode
     * @param array $element
     * @return array
     */
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

    /**
     * Transform depends configuration
     *
     * @param array $config
     * @return array
     */
    protected function _transformElementDepends(array $config)
    {
        $result = array();
        foreach ($config as $nodeName => $nodeValue) {
            $element = array();
            $element['nodeName'] = 'field';
            $element['@attributes']['id'] = $nodeName;
            $attributes = $this->_getValue($nodeValue, '@attributes', array());
            $element = $this->_transformAttributes($attributes, $element);

            if (false === empty($attributes)) {
                unset($nodeValue['@attributes']);
            }

            $element['#text'] = $nodeValue['#text'];
            $result[] = $element;
        }

        return $result;
    }

    /**
     * Transform element configuration
     *
     * @param array $config
     * @return array
     */
    protected function _transformElementAttribute(array $config)
    {
        $result = array();
        foreach ($config as $nodeName => $nodeValue) {
            $element = array();
            $element['nodeName'] = $nodeName;
            $attributes = $this->_getValue($nodeValue, '@attributes', array());
            $element = $this->_transformAttributes($attributes, $element);

            if (false === empty($attributes)) {
                unset($nodeValue['@attributes']);
            }
            if ($this->_isSubConfigValue($nodeValue)) {
                $element['subConfig'] = $this->_transformElementAttribute($nodeValue);
            } else {
                if ($this->_getValue($nodeValue, '#text', false)) {
                    $element['#text'] = $this->_getValue($nodeValue, '#text');
                }
                if ($this->_getValue($nodeValue, '#cdata-section', false)) {
                    $element['#cdata-section'] = $this->_getValue($nodeValue, '#cdata-section');
                }
            }

            $result[] = $element;
        }

        return $result;
    }
}
