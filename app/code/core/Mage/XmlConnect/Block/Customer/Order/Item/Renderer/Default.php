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
 * Customer order details item xml
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Default extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    /**
     * Add item to XML object
     * (get from template: Mage_Sales::order/items/renderer/default.phtml)
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return null
     */
    public function addItemToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        /** @var $item Mage_Sales_Model_Order_Item */
        $item = $this->getItem();

        /** @var $itemXml Mage_XmlConnect_Model_Simplexml_Element */
        $itemXml = $orderItemXmlObj->addCustomChild('item', null, array(
            'product_id' => $item->getProductId()
        ));
        $itemXml->addCustomChild('name', $item->getName());

        /** @var $weeeHelper Mage_Weee_Helper_Data */
        $weeeHelper = $this->helper('Mage_Weee_Helper_Data');
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper  = $this->helper('Mage_Tax_Helper_Data');

        Mage::helper('Mage_XmlConnect_Helper_Customer_Order')->addItemOptionsToXml($this, $itemXml);

        $addtInfoBlock = $this->getProductAdditionalInformationBlock();
        if ($addtInfoBlock) {
            // TODO: find how to set additional info block
            // $addtInfoBlock->setItem($item)->toHtml();
        }

        $itemXml->addCustomChild('entity_type', $item->getProductType());
        $itemXml->addCustomChild('description', $item->getDescription());
        $itemXml->addCustomChild('sku', Mage::helper('Mage_Core_Helper_String')->splitInjection($this->getSku()));

        $this->setWeeeTaxAppliedAmount($item->getWeeeTaxAppliedAmount());
        $this->setWeeeTaxDisposition($item->getWeeeTaxDisposition());

        $typeOfDisplay1 = $weeeHelper->typeOfDisplay($item, 1, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay2 = $weeeHelper->typeOfDisplay($item, 2, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay4 = $weeeHelper->typeOfDisplay($item, 4, 'sales')
            && $this->getWeeeTaxAppliedAmount();
        $typeOfDisplay014 = $weeeHelper->typeOfDisplay($item, array(0, 1, 4), 'sales')
            && $this->getWeeeTaxAppliedAmount();

        $this->setTypesOfDisplay(array(
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_1   => $typeOfDisplay1,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_2   => $typeOfDisplay2,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_4   => $typeOfDisplay4,
            Mage_XmlConnect_Helper_Customer_Order::PRICE_DISPLAY_TYPE_14  => $typeOfDisplay014,
        ));
        $this->setWeeeTaxes($weeeHelper->getApplied($item));

        /** @var $priceXml Mage_XmlConnect_Model_Simplexml_Element */
        $priceXml = $itemXml->addChild('price');

        // Quantity: Ordered, Shipped, Cancelled, Refunded
        Mage::helper('Mage_XmlConnect_Helper_Customer_Order')->addQuantityToXml($this, $itemXml->addChild('qty'), $item);

        /** @var $subtotalXml Mage_XmlConnect_Model_Simplexml_Element */
        $subtotalXml = $itemXml->addChild('subtotal');

        // Price & subtotal - excluding tax
        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceExclTax()) {
            Mage::helper('Mage_XmlConnect_Helper_Customer_Order')->addPriceAndSubtotalToXml($this, $item, $priceXml, $subtotalXml);
        }

        // Price & subtotal - including tax
        if ($taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax()) {
            Mage::helper('Mage_XmlConnect_Helper_Customer_Order')->addPriceAndSubtotalToXml(
                $this, $item, $priceXml, $subtotalXml, true
            );
        }
    }
}
