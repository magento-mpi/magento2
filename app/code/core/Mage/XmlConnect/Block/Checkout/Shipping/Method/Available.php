<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout shipping methods xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Shipping_Method_Available
    extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /**
     * Render shipping methods xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $methodsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $methodsXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<shipping_methods></shipping_methods>'));
        $_shippingRateGroups = $this->getShippingRates();
        if ($_shippingRateGroups) {
            $store = $this->getQuote()->getStore();
            $_sole = count($_shippingRateGroups) == 1;
            foreach ($_shippingRateGroups as $code => $_rates) {
                $methodXmlObj = $methodsXmlObj->addChild('method');
                $methodXmlObj->addAttribute('label', $methodsXmlObj->escapeXml($this->getCarrierName($code)));
                $ratesXmlObj = $methodXmlObj->addChild('rates');

                $_sole = $_sole && count($_rates) == 1;
                foreach ($_rates as $_rate) {
                    $rateXmlObj = $ratesXmlObj->addChild('rate');
                    $rateXmlObj->addAttribute('label', $methodsXmlObj->escapeXml($_rate->getMethodTitle()));
                    $rateXmlObj->addAttribute('code', $_rate->getCode());
                    if ($_rate->getErrorMessage()) {
                        $rateXmlObj->addChild('error_message', $methodsXmlObj->escapeXml($_rate->getErrorMessage()));
                    } else {
                        $price = Mage::helper('Mage_Tax_Helper_Data')->getShippingPrice(
                            $_rate->getPrice(),
                            Mage::helper('Mage_Tax_Helper_Data')->displayShippingPriceIncludingTax(),
                            $this->getAddress()
                        );
                        $formattedPrice = $store->convertPrice($price, true, false);
                        $rateXmlObj->addAttribute('price', Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml(
                            $store->convertPrice($price, false, false)
                        ));
                        $rateXmlObj->addAttribute('formated_price', $formattedPrice);
                    }
                }
            }
        } else {
            Mage::throwException($this->__('Shipping to this address is not possible.'));
        }
        return $methodsXmlObj->asNiceXml();
    }
}
