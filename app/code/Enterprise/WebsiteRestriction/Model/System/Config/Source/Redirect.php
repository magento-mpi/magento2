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
 * Sys config source model for private sales redirect modes
 *
 */
class Enterprise_WebsiteRestriction_Model_System_Config_Source_Redirect
extends Varien_Object
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
                'value' => Enterprise_WebsiteRestriction_Model_Mode::HTTP_302_LOGIN,
                'label' => Mage::helper('Enterprise_WebsiteRestriction_Helper_Data')->__('To login form (302 Found)'),
            ),
            array(
                'value' => Enterprise_WebsiteRestriction_Model_Mode::HTTP_302_LANDING,
                'label' => Mage::helper('Enterprise_WebsiteRestriction_Helper_Data')->__('To landing page (302 Found)'),
            ),
        );
    }
}