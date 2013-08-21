<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for DHL Content Type
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Model_Shipping_Carrier_Dhl_International_Source_Contenttype
{
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => __('Documents'),
                'value' => Magento_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_DOC),
            array('label' => __('Non documents'),
                'value' => Magento_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_NON_DOC),
        );
    }
}
