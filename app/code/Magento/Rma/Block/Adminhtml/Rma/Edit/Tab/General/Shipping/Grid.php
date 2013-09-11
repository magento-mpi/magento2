<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid of packaging shipment
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

class Grid extends \Magento\Adminhtml\Block\Template
{
    /**
     * Return collection of shipment items
     *
     * @return array
     */
    public function getCollection()
    {
        return \Mage::registry('current_rma')->getShippingMethods(true);
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = \Mage::registry('current_rma')->getStoreId();
        $order = \Mage::registry('current_rma')->getOrder();
        $address = $order->getShippingAddress();
        $shippingSourceCountryCode = $address->getCountryId();

        $shippingDestinationInfo = \Mage::helper('Magento\Rma\Helper\Data')->getReturnAddressModel($storeId);
        $shippingDestinationCountryCode = $shippingDestinationInfo->getCountryId();

        if ($shippingSourceCountryCode != $shippingDestinationCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Format price
     *
     * @param   decimal $value
     * @return  double
     */
    public function formatPrice($value)
    {
        return sprintf('%.2F', $value);
    }
}
