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
class Mage_Captcha_Model_Config_Mode
{
    /**
     * Get options for captcha mode selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => __('Always'),
                'value' => Mage_Captcha_Helper_Data::MODE_ALWAYS
            ),
            array(
                'label' => __('After number of attempts to login'),
                'value' => Mage_Captcha_Helper_Data::MODE_AFTER_FAIL
            ),
        );
    }
}
