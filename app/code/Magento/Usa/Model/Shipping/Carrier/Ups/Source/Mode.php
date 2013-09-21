<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * UPS (UPS XML) mode source model
 *
 * @deprecated  since 1.7.0.0
 * @category    Magento
 * @package     Magento_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Model_Shipping_Carrier_Ups_Source_Mode implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => __('Live')),
            array('value' => '0', 'label' => __('Development')),
        );
    }
}
