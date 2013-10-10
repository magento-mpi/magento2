<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image config field renderer
 */
namespace Magento\Backend\Block\System\Config\Form\Field;

class Image extends \Magento\Data\Form\Element\Image
{
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        $url = parent::_getUrl();
        $config = $this->getFieldConfig();
        /* @var $config array */
        if (array_key_exists('base_url', $config)) {
            $element = $config['base_url'];
            $urlType = empty($element['type']) ? 'link' : (string)$element['type'];
            $url = $this->_urlBuilder->getBaseUrl($urlType) . $element['value'] . '/' . $url;
        }
        return $url;
    }
}
