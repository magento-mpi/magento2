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
class Core_Mage_OrderShipment_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Provides partial or fill shipment
     *
     * @param array $shipmentData
     */
    public function createShipmentAndVerifyProductQty(array $shipmentData = array())
    {
        $verify = array();
        $this->clickButton('ship');
        foreach ($shipmentData as $options) {
            if (!is_array($options)) {
                continue;
            }
            $productQty = (isset($options['ship_product_qty'])) ? $options['ship_product_qty'] : '%noValue%';
            if (isset($options['ship_product_sku'])) {
                $sku = $options['ship_product_sku'];
                $verify[$sku] = $productQty;
                $this->addParameter('sku', $sku);
                $this->fillField('qty_to_ship', $productQty);
            }
        }
        if (!$verify) {
            $productCount = $this->getControlCount('fieldset', 'product_line_to_ship');
            for ($i = 1; $i <= $productCount; $i++) {
                $this->addParameter('productNumber', $i);
                $prodSku = $this->getControlAttribute('field', 'product_sku', 'text');
                $pointer = 'SKU: ';
                $prodSku = substr($prodSku, strpos($prodSku, $pointer) + strlen($pointer));
                $prodQty = $this->getControlAttribute('field', 'product_qty', 'selectedValue');
                $verify[$prodSku] = $prodQty;
            }
        }
        $this->clickButton('submit_shipment');
        $this->assertMessagePresent('success', 'success_creating_shipment');
        foreach ($verify as $productSku => $qty) {
            if ($qty == '%noValue%') {
                continue;
            }
            $this->addParameter('sku', $productSku);
            $this->addParameter('shippedQty', $qty);
            $this->assertTrue($this->controlIsPresent('field', 'qty_shipped'),
                'Qty of shipped products is incorrect at the orders form');
        }
    }

    /**
     * Create Shipment for existing order
     *
     * @param array $orderData
     * @param array $shipmentData
     */
    public function openOrderAndCreateShipment(array $orderData, array $shipmentData = array())
    {
        $this->orderHelper()->openOrder($orderData);
        $this->createShipmentAndVerifyProductQty($shipmentData);
    }
}