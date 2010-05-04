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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout order info xml renderer
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Order_Review_Info extends Mage_Checkout_Block_Onepage_Review_Info
{
    /**
     * Render order review items
     *
     * @return string
     */
    protected function _toHtml()
    {
        $itemsXmlObj = new Varien_Simplexml_Element('<products></products>');
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach($this->getItems() as $item){
            $type = $this->_getItemType($item);
            $renderer = $this->getItemRenderer($type)->setItem($item);

            /**
             * General information
             */
            $itemXml = $itemsXmlObj->addChild('item');
            $itemXml->addChild('entity_id', $item->getProduct()->getId());
            $itemXml->addChild('entity_type', $type);
            $itemXml->addChild('item_id', $item->getId());
            $itemXml->addChild('name', $itemsXmlObj->xmlentities(strip_tags($renderer->getProductName())));
            $itemXml->addChild('qty', $renderer->getQty());
            $itemXml->addChild('icon', $renderer->getProductThumbnail()->resize(Mage_XmlConnect_Block_Catalog_Product::PRODUCT_IMAGE_SMALL_RESIZE_PARAM));

            /**
             * Price
             */
            $price = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()){
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $price = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount() + $item->getWeeeTaxDisposition();
                }
                else {
                    $price = $item->getCalculationPrice();
                }
            }
            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()){
                $_incl = $this->helper('checkout')->getPriceInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $price = $_incl + $item->getWeeeTaxAppliedAmount();
                }
                else {
                    $price = $_incl - $item->getWeeeTaxDisposition();
                }
            }
            $price = sprintf('%01.2f', $price);
            $formatedPrice = $quote->getStore()->formatPrice($price, false);

            $itemXml->addChild('price', $price);
            $itemXml->addChild('formated_price', $formatedPrice);

            /**
             * Subtotal
             */
            $price = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()){
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $price = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount() + $item->getWeeeTaxRowDisposition();
                }
                else {
                    $price = $item->getRowTotal();
                }
            }
            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()){
                $_incl = $this->helper('checkout')->getSubtotalInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $price = $_incl + $item->getWeeeTaxAppliedRowAmount();
                }
                else {
                    $price = $_incl - $item->getWeeeTaxRowDisposition();
                }
            }
            $price = sprintf('%01.2f', $price);
            $formatedPrice = $quote->getStore()->formatPrice($price, false);
            $itemXml->addChild('subtotal', $price);
            $itemXml->addChild('formated_subtotal', $formatedPrice);

            /**
             * Options list
             */
            if ($_options = $renderer->getOptionList()){
                $itemOptionsXml = $itemXml->addChild('options');
                foreach ($_options as $_option){
                    $_formatedOptionValue = $renderer->getFormatedOptionValue($_option);
                    $optionXml = $itemOptionsXml->addChild('option');
                    $optionXml->addAttribute('label', $itemsXmlObj->xmlentities(strip_tags($_option['label'])));
                    $optionXml->addAttribute('text', $itemsXmlObj->xmlentities(strip_tags($_formatedOptionValue['value'])));
//                    if (isset($_formatedOptionValue['full_view'])){
//                        $label = strip_tags($_option['label']);
//                        $value = strip_tags($_formatedOptionValue['full_view']);
//                    }
                }
            }
        }

        return $itemsXmlObj->asNiceXml();
    }

}