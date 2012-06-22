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
class Core_Mage_OrderShipment_Helper extends Mage_Selenium_TestCase
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
            if (is_array($options)) {
                $sku = (isset($options['ship_product_sku'])) ? $options['ship_product_sku'] : null;
                $productQty = (isset($options['ship_product_qty'])) ? $options['ship_product_qty'] : '%noValue%';
                if ($sku) {
                    $verify[$sku] = $productQty;
                    $this->addParameter('sku', $sku);
                    $this->fillField('qty_to_ship', $productQty);
                }
            }
        }
        if (!$verify) {
            $productCount = $this->getXpathCount($this->_getControlXpath('fieldset', 'product_line_to_ship'));
            for ($i = 1; $i <= $productCount; $i++) {
                $this->addParameter('productNumber', $i);
                $skuXpath = $this->_getControlXpath('field', 'product_sku');
                $qtyXpath = $this->_getControlXpath('field', 'product_qty');
                $prodSku = trim(preg_replace('/SKU:|\\n/', '', $this->getText($skuXpath)));
                $prodQty = $this->getAttribute($qtyXpath . '/@value');
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
            $xpathShipped = $this->_getControlXpath('field', 'qty_shipped');
            $this->assertTrue($this->isElementPresent($xpathShipped),
                'Qty of shipped products is incorrect at the orders form');
        }
    }
}