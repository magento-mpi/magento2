<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Magento
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Captcha_Model_Config_Font
{
    /**
     * Get options for font selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        foreach (Mage::helper('Magento_Captcha_Helper_Data')->getFonts() as $fontName => $fontData) {
            $optionArray[] = array('label' => $fontData['label'], 'value' => $fontName);
        }
        return $optionArray;
    }
}
