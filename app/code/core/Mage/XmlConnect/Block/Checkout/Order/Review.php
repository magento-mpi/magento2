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
 * One page checkout order review xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Order_Review extends Mage_Checkout_Block_Onepage_Review
{
    /**
     * Render order review xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $orderXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<order></order>'));

        /**
         * Order items
         */
        $products = $this->getChildHtml('order_products');
        if ($products) {
            $productsXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
                array('data' => $products));
            $orderXmlObj->appendChild($productsXmlObj);
        }

        /**
         * Totals
         */
        $totalsXml = $this->getChildHtml('totals');
        if ($totalsXml) {
            $totalsXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
                array('data' => $totalsXml));
            $orderXmlObj->appendChild($totalsXmlObj);
        }

        /**
         * Agreements
         */
        $agreements = $this->getChildHtml('agreements');
        if ($agreements) {
            $agreementsXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
                array('data' => $agreements));
            $orderXmlObj->appendChild($agreementsXmlObj);
        }

        return $orderXmlObj->asNiceXml();
    }
}
