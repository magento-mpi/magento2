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
class Magento_Captcha_Model_Config_Mode implements Magento_Core_Model_Option_ArrayInterface
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
                'value' => Magento_Captcha_Helper_Data::MODE_ALWAYS
            ),
            array(
                'label' => __('After number of attempts to login'),
                'value' => Magento_Captcha_Helper_Data::MODE_AFTER_FAIL
            ),
        );
    }
}
