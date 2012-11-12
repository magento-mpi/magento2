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
 * Shopping cart summary information xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Info extends Mage_XmlConnect_Block_Cart
{
    /**
     * Render cart summary xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->getQuote();
        /** @var $xmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObject  = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element', array('data' => '<cart></cart>'));

        $xmlObject->addChild('is_virtual', (int)$this->helper('Mage_Checkout_Helper_Cart')->getIsVirtualQuote());

        $xmlObject->addChild('summary_qty', (int)$this->helper('Mage_Checkout_Helper_Cart')->getSummaryCount());

        $xmlObject->addChild('virtual_qty', (int)$quote->getItemVirtualQty());

        if (strlen($quote->getCouponCode())) {
            $xmlObject->addChild('has_coupon_code', 1);
        }

        $totalsXml = $this->getChildHtml('totals');
        if ($totalsXml) {
            /** @var $totalsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
            $totalsXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element', array('data' => $totalsXml));
            $xmlObject->appendChild($totalsXmlObj);
        }
        return $xmlObject->asNiceXml();
    }
}
