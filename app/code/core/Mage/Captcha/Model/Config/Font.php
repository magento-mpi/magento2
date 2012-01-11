<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Config_Font
{
    /**
     * Get options for font selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        foreach (Mage::helper('Mage_Captcha_Helper_Data')->getFonts() as $fontName => $fontData) {
            $optionArray[] = array('label' => $fontData['label'], 'value' => $fontName);
        }
        return $optionArray;
    }
}
