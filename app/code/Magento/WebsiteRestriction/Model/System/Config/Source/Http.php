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
 * Sys config source model for stub page statuses
 *
 */
class Magento_WebsiteRestriction_Model_System_Config_Source_Http
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
                'value' => Magento_WebsiteRestriction_Model_Mode::HTTP_503,
                'label' => Mage::helper('Magento_WebsiteRestriction_Helper_Data')->__('503 Service Unavailable'),
            ),
            array(
                'value' => Magento_WebsiteRestriction_Model_Mode::HTTP_200,
                'label' => Mage::helper('Magento_WebsiteRestriction_Helper_Data')->__('200 OK'),
            ),
        );
    }
}
