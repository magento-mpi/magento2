<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderShipment_Helper extends Mage_Selenium_TestCase
{
   /**
    * Provides partial or fill shipment
    *
    * @param array $shipmentData
    */
    public function createShipment(array $shipmentData)
    {
        $this->clickButton('ship');
        $shipmentData = $this->arrayEmptyClear($shipmentData);
        foreach($shipmentData as $product => $options) {
            if (array_key_exists('filter_sku', $options)) {
                $this->addParameter('sku', $options['filter_sku']);
                if (array_key_exists('qty_to_ship', $options)) {
                    $this->fillForm(array('qty_to_ship' => $options['qty_to_ship']));
                }
            }
        }
        $this->clickButton('submit_shipment');
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
        foreach($shipmentData as $product => $options) {
            if (array_key_exists('filter_sku', $options)) {
                $this->addParameter('sku', $options['filter_sku']);
                if (array_key_exists('qty_to_ship', $options)) {
                    $this->addParameter('shippedQty', $options['qty_to_ship']);
                    $xpathShipped = $this->_getControlXpath('field', 'qty_shipped');
                    $this->assertTrue($this->isElementPresent($xpathShipped),
                        'Qty of shipped products is incorrect at the orders form');
                }
            }
        }
    }
}
