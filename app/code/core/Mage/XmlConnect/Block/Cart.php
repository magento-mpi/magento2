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
 * Shopping cart xml renderer
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart extends Mage_Checkout_Block_Cart_Abstract
{
   /**
     * Render shopping cart xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $cartMessages   = $this->getMessages();
        $quote          = $this->getQuote();
        $xmlObject      = new Varien_Simplexml_Element('<cart></cart>');
        $xmlObject->addAttribute('is_virtual', (int)$this->helper('checkout/cart')->getIsVirtualQuote());
        $xmlObject->addAttribute('summary_qty', (int)$this->helper('checkout/cart')->getSummaryCount());
        if (strlen($quote->getCouponCode())) {
            $xmlObject->addAttribute('has_coupon_code', 1);
        }
        $products = $xmlObject->addChild('products');

        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $type = $item->getProductType();
            $renderer = $this->getItemRenderer($type)->setItem($item);

            /**
             * General information
             */
            $itemXml = $products->addChild('item');
            $itemXml->addChild('entity_id', $item->getProduct()->getId());
            $itemXml->addChild('entity_type', $type);
            $itemXml->addChild('item_id', $item->getId());
            $itemXml->addChild('name', $xmlObject->xmlentities(strip_tags($renderer->getProductName())));
            $itemXml->addChild('code', 'cart[' . $item->getId() . '][qty]');
            $itemXml->addChild('qty', $renderer->getQty());
            $itemXml->addChild('icon', $renderer->getProductThumbnail()->resize(Mage_XmlConnect_Block_Catalog_Product::PRODUCT_IMAGE_SMALL_RESIZE_PARAM));

            /**
             * Price
             */
            $exclPrice = $inclPrice = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()){
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $exclPrice = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount() + $item->getWeeeTaxDisposition();
                }
                else {
                    $exclPrice = $item->getCalculationPrice();
                }
            }

            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()){
                $_incl = $this->helper('checkout')->getPriceInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $inclPrice = $_incl + $item->getWeeeTaxAppliedAmount();
                }
                else {
                    $inclPrice = $_incl - $item->getWeeeTaxDisposition();
                }
            }

            $exclPrice = sprintf('%01.2f', $exclPrice);
            $formatedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);

            $inclPrice = sprintf('%01.2f', $inclPrice);
            $formatedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $priceXmlObj = $itemXml->addChild('price');
            $formatedPriceXmlObj = $itemXml->addChild('formated_price');

            if ($this->helper('tax')->displayCartBothPrices()) {
                 $priceXmlObj->addAttribute('excluding_tax', $exclPrice);
                 $priceXmlObj->addAttribute('including_tax', $inclPrice);

                 $formatedPriceXmlObj->addAttribute('excluding_tax', $formatedExclPrice);
                 $formatedPriceXmlObj->addAttribute('including_tax', $formatedInclPrice);
            }
            else {
                 if ($this->helper('tax')->displayCartPriceExclTax()) {
                     $priceXmlObj->addAttribute('regular', $exclPrice);
                     $formatedPriceXmlObj->addAttribute('regular', $formatedExclPrice);
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                     $priceXmlObj->addAttribute('regular', $inclPrice);
                     $formatedPriceXmlObj->addAttribute('regular', $formatedInclPrice);
                }
            }


            /**
             * Subtotal
             */
            $exclPrice = $inclPrice = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()){
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $exclPrice = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount() + $item->getWeeeTaxRowDisposition();
                }
                else {
                    $exclPrice = $item->getRowTotal();
                }
            }
            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()){
                $_incl = $this->helper('checkout')->getSubtotalInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales') && $item->getWeeeTaxAppliedAmount()){
                    $inclPrice = $_incl + $item->getWeeeTaxAppliedRowAmount();
                }
                else {
                    $inclPrice = $_incl - $item->getWeeeTaxRowDisposition();
                }
            }

            $exclPrice = sprintf('%01.2f', $exclPrice);
            $formatedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);

            $inclPrice = sprintf('%01.2f', $inclPrice);
            $formatedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $subtotalPriceXmlObj = $itemXml->addChild('subtotal');
            $subtotalFormatedPriceXmlObj = $itemXml->addChild('formated_subtotal');

            if ($this->helper('tax')->displayCartBothPrices()) {
                 $subtotalPriceXmlObj->addAttribute('excluding_tax', $exclPrice);
                 $subtotalPriceXmlObj->addAttribute('including_tax', $inclPrice);

                 $subtotalFormatedPriceXmlObj->addAttribute('excluding_tax', $formatedExclPrice);
                 $subtotalFormatedPriceXmlObj->addAttribute('including_tax', $formatedInclPrice);
            }
            else {
                 if ($this->helper('tax')->displayCartPriceExclTax()) {
                     $subtotalPriceXmlObj->addAttribute('regular', $exclPrice);
                     $subtotalFormatedPriceXmlObj->addAttribute('regular', $formatedExclPrice);
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                     $subtotalPriceXmlObj->addAttribute('regular', $inclPrice);
                     $subtotalFormatedPriceXmlObj->addAttribute('regular', $formatedInclPrice);
                }
            }

            /**
             * Options list
             */
            if ($_options = $renderer->getOptionList()){
                $itemOptionsXml = $itemXml->addChild('options');
                foreach ($_options as $_option){
                    $_formatedOptionValue = $renderer->getFormatedOptionValue($_option);
                    $optionXml = $itemOptionsXml->addChild('option');
                    $optionXml->addAttribute('label', $xmlObject->xmlentities(strip_tags($_option['label'])));
                    $optionXml->addAttribute('text', $xmlObject->xmlentities(strip_tags($_formatedOptionValue['value'])));
//                    if (isset($_formatedOptionValue['full_view'])){
//                        $label = strip_tags($_option['label']);
//                        $value = strip_tags($_formatedOptionValue['full_view']);
//                    }
                }
            }

            /**
             * Item messages
             */
            if ($messages = $renderer->getMessages()){
                $itemMessagesXml = $itemXml->addChild('messages');
                foreach ($messages as $message){
                    $messageXml = $itemMessagesXml->addChild('option');
                    $messageXml->addChild('type', $message['type']);
                    $messageXml->addChild('text', $xmlObject->xmlentities(strip_tags($message['text'])));
                }
            }
        }

        /**
         * Cart messages
         */
        if ($cartMessages) {
            $messagesXml = $xmlObject->addChild('messages');
            foreach ($cartMessages as $status => $messages) {
                foreach ($messages as $message) {
                    $messageXml = $messagesXml->addChild('message');
                    $messageXml->addChild('status', $status);
                    $messageXml->addChild('text', strip_tags($message));
                }
            }
        }

        /**
         * Cross Sell Products
         */
        $crossSellXmlObj = new Varien_Simplexml_Element($this->getChildHtml('crosssell'));
        $xmlObject->appendChild($crossSellXmlObj);

        return $xmlObject->asNiceXml();
    }

}
