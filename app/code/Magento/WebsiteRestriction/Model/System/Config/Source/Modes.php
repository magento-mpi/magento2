<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sys config source model for restriction modes
 *
 */
class Magento_WebsiteRestriction_Model_System_Config_Source_Modes
extends Magento_Object
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_WebsiteRestriction_Model_Mode::ALLOW_NONE,
                'label' => Mage::helper('Magento_WebsiteRestriction_Helper_Data')->__('Website Closed'),
            ),
            array(
                'value' => Magento_WebsiteRestriction_Model_Mode::ALLOW_LOGIN,
                'label' => Mage::helper('Magento_WebsiteRestriction_Helper_Data')->__('Private Sales: Login Only'),
            ),
            array(
                'value' => Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER,
                'label' => Mage::helper('Magento_WebsiteRestriction_Helper_Data')->__('Private Sales: Login and Register'),
            ),
        );
    }
}
