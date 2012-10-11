<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_OrderShipment
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_OrderShipment_Helper extends Core_Mage_OrderShipment_Helper
{
    /**
     * Create Shipment for existing order
     *
     * @param array $orderData
     * @param array $shipmentData
     */
    public function openOrderAndCreateShipment(array $orderData, array $shipmentData = array())
    {
        if (isset($orderData['filter_order_id'])) {
            $this->addParameter('order_id', '#' . $orderData['filter_order_id']);
        } else {
            $this->fail("Not enough parameters for opening order. 'filter_order_id' parameter is not set");
        }
        $this->searchAndOpen($orderData);
        $this->createShipmentAndVerifyProductQty($shipmentData);
    }
}
