<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sys config source model for restriction modes
 *
 */
class Enterprise_WebsiteRestriction_Model_System_Config_Source_Modes
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
                'value' => Enterprise_WebsiteRestriction_Model_Mode::ALLOW_NONE,
                'label' => Mage::helper('Enterprise_WebsiteRestriction_Helper_Data')->__('Website Closed'),
            ),
            array(
                'value' => Enterprise_WebsiteRestriction_Model_Mode::ALLOW_LOGIN,
                'label' => Mage::helper('Enterprise_WebsiteRestriction_Helper_Data')->__('Private Sales: Login Only'),
            ),
            array(
                'value' => Enterprise_WebsiteRestriction_Model_Mode::ALLOW_REGISTER,
                'label' => Mage::helper('Enterprise_WebsiteRestriction_Helper_Data')->__('Private Sales: Login and Register'),
            ),
        );
    }
}
