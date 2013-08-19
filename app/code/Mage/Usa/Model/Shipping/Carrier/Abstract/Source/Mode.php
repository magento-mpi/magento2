<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shippers Modesource model
 *
 * @category Mage
 * @package Mage_Usa
 * @author Magento Core Team <core@magentocommerce.com>
 */

class Mage_Usa_Model_Shipping_Carrier_Abstract_Source_Mode
{
    /**
     * Returns array to be used in packages request type on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => __('Development')),
            array('value' => '1', 'label' => __('Live')),
        );
    }
}
