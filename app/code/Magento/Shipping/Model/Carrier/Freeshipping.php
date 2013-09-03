<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Free shipping model
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Shipping_Model_Carrier_Freeshipping
    extends Magento_Shipping_Model_Carrier_Abstract
    implements Magento_Shipping_Model_Carrier_Interface
{

    protected $_code = 'freeshipping';
    protected $_isFixed = true;

    /**
     * FreeShipping Rates Collector
     *
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return Magento_Shipping_Model_Rate_Result
     */
    public function collectRates(Magento_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('Magento_Shipping_Model_Rate_Result');

        $this->_updateFreeMethodQuote($request);

        if (($request->getFreeShipping())
            || ($request->getBaseSubtotalInclTax() >= $this->getConfigData('free_shipping_subtotal'))
        ) {
            $method = Mage::getModel('Magento_Shipping_Model_Rate_Result_Method');

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        }

        return $result;
    }

    /**
     * Allows free shipping when all product items have free shipping (promotions etc.)
     *
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return void
     */
    protected function _updateFreeMethodQuote($request)
    {
        $freeShipping = false;
        $items = $request->getAllItems();
        $c = count($items);
        for ($i = 0; $i < $c; $i++) {
            if ($items[$i]->getProduct() instanceof Magento_Catalog_Model_Product) {
                if ($items[$i]->getFreeShipping()) {
                    $freeShipping = true;
                } else {
                    return;
                }
            }
        }
        if ($freeShipping) {
            $request->setFreeShipping(true);
        }
    }

    public function getAllowedMethods()
    {
        return array('freeshipping'=>$this->getConfigData('name'));
    }

}
