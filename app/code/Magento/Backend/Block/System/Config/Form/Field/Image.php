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
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_System_Config_Form_Field_Image extends Magento_Data_Form_Element_Image
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
            $url = Mage::getBaseUrl($urlType) . $element['value'] . '/' . $url;
        }

        return $url;
    }

}
