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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer order details xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Details extends Mage_Payment_Block_Info
{
    /**
     * Render customer orders list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $orderXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $orderXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<order_details></order_details>');
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('current_order');
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->__('Model of order is not loaded.'));
        }
        $orderDate = $this->formatDate($order->getCreatedAtStoreDate(), 'long');
        $orderXmlObj->addCustomChild(
            'order',
            null,
            array(
                 'label' => Mage::helper('sales')->__('Order #%s - %s', $order->getRealOrderId(), $order->getStatusLabel()),
                 'order_date' => Mage::helper('sales')->__('Order Date: %s', $orderDate)
            )
        );
        if (!$order->getIsVirtual()) {
            $shipping = preg_replace(
                array('@\r@', '@\n+@'),
                array('', "\n"),
                $order->getShippingAddress()->format('text')
            );
            $billing = preg_replace(
                array('@\r@', '@\n+@'),
                array('', "\n"),
                $order->getBillingAddress()->format('text')
            );
            $orderXmlObj->addCustomChild('shipping_address', $shipping);
            $orderXmlObj->addCustomChild('billing_address', $billing);

            if ($order->getShippingDescription()) {
                $shippingMethodDescription = $order->getShippingDescription();
            } else {
                $shippingMethodDescription = Mage::helper('sales')->__('No shipping information available');
            }
            $orderXmlObj->addCustomChild('shipping_method', $shippingMethodDescription);
        }
        /**
         * Pre-defined array of methods that we are going to render
         */
        $methodArray = array(
            'ccsave' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Ccsave',
            'checkmo' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Checkmo',
            'purchaseorder' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Purchaseorder',
            'authorizenet' => 'Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Authorizenet',
        );
//        /**
//         * Check is available Payment Bridge and add methods for rendering
//         */
//        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_Pbridge'))) {
//            $pbridgeMethodArray = array(
//                'pbridge_authorizenet'  => 'Enterprise_Pbridge_Model_Payment_Method_Authorizenet',
//                'pbridge_paypal'        => 'Enterprise_Pbridge_Model_Payment_Method_Paypal',
//                'pbridge_verisign'      => 'Enterprise_Pbridge_Model_Payment_Method_Payflow_Pro',
//                'pbridge_paypaluk'      => 'Enterprise_Pbridge_Model_Payment_Method_Paypaluk',
//            );
//            $methodArray = $methodArray + $pbridgeMethodArray;
//        }
        $method = $this->helper('payment')->getInfoBlock($order->getPayment())->getMethod();
        $methodCode = $method->getCode();

        $paymentNode = $orderXmlObj->addChild('payment_method');
        if (array_key_exists($methodCode, $methodArray)) {
            $currentBlockRenderer = 'xmlconnect/checkout_payment_method_info_' . $methodCode;
            $currentBlockName = 'xmlconnect.checkout.payment.method.info.' . $methodCode;
            $this->getLayout()->addBlock($currentBlockRenderer, $currentBlockName);
            $this->setChild($methodCode, $currentBlockName);
            $renderer = $this->getChild($methodCode)->setInfo($order->getPayment());
            $renderer->addPaymentInfoToXmlObj($paymentNode);
        } else {
            $paymentNode->addAttribute('type', $methodCode);
            $paymentNode->addAttribute('label', $this->escapeHtml($method->getTitle()));

            $this->setInfo($order->getPayment());

            $specificInfo = array_merge(
                (array)$order->getPayment()->getAdditionalInformation(),
                (array)$this->getSpecificInformation()
            );
            if (!empty($specificInfo)) {
                foreach ($specificInfo as $label => $value) {
                    if ($value) {
                        $paymentNode->addCustomChild(
                            'item',
                            implode($this->getValueAsArray($value, true), PHP_EOL),
                            array(
                                 'label' => $this->escapeHtml($label)
                            )
                        );
                    }
                }
            }
        }

        if ($itemsBlock = $this->getLayout()->getBlock('xmlconnect.customer.order.items')) {
            /** @var $itemsBlock Mage_XmlConnect_Block_Customer_Order_Items */
            $itemsBlock->setItems($order->getItemsCollection());
            $itemsBlock->addItemsToXmlObject($orderXmlObj);
            if ($totalsBlock = $this->getLayout()->getBlock('xmlconnect.customer.order.totals')) {
                $totalsBlock->setOrder($order);
                $totalsBlock->addTotalsToXmlObject($orderXmlObj);
            }
        } else {
            $orderXmlObj->addChild('ordered_items');
        }

        return $orderXmlObj->asNiceXml();
    }

    /**
     * Add XML nodes with items
     *
     * @param array $items
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObject
     * @return viod
     */
    protected function _getItemsXml($items, $xmlObject)
    {
        if (count($items)) {
            $taxHelper = $this->helper('tax');
            foreach ($items as $item) {
                $itemXml = $xmlObject->addChild('item');

                $type = $item->getProductType();
                $renderer = $this->getItemRenderer($type)->setItem($item);

                /**
                 * General information
                 */
                $itemXml->addChild('entity_id', $item->getId());
                $itemXml->addChild('entity_type', $type);
                $itemXml->addChild('name', $xmlObject->xmlentities(strip_tags($renderer->getProductName())));
                $itemXml->addChild('qty', $renderer->getQty());

                /**
                 * Price
                 */
                $exclPrice = $inclPrice = 0.00;
                if ($taxHelper->displaySalesPriceExclTax() || $taxHelper->displaySalesBothPrices()) {
                    if (Mage::helper('weee')->typeOfDisplay($item, array(
                                                                        0, 1, 4
                                                                   ), 'sales')
                        && $item->getWeeeTaxAppliedAmount()
                    ) {
                        $exclPrice = $item->getCalculationPrice()
                                     + $item->getWeeeTaxAppliedAmount()
                                     + $item->getWeeeTaxDisposition();
                    } else {
                        $exclPrice = $item->getCalculationPrice();
                    }
                }
                if ($taxHelper->displaySalesPriceInclTax() || $taxHelper->displaySalesBothPrices()) {
                    $_incl = $this->helper('checkout')->getPriceInclTax($item);
                    if (Mage::helper('weee')->typeOfDisplay($item, array(
                                                                        0, 1, 4
                                                                   ), 'sales')
                        && $item->getWeeeTaxAppliedAmount()
                    ) {
                        $inclPrice = $_incl + $item->getWeeeTaxAppliedAmount();
                    } else {
                        $inclPrice = $_incl - $item->getWeeeTaxDisposition();
                    }
                }
                $exclPrice = Mage::helper('xmlconnect')->formatPriceForXml($exclPrice);
                $formatedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);
                $inclPrice = Mage::helper('xmlconnect')->formatPriceForXml($inclPrice);
                $formatedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);
                $priceXmlObj = $itemXml->addChild('price');
                $formatedPriceXmlObj = $itemXml->addChild('formated_price');
                if ($taxHelper->displaySalesBothPrices()) {
                    $priceXmlObj->addAttribute('excluding_tax', $exclPrice);
                    $priceXmlObj->addAttribute('including_tax', $inclPrice);
                    $formatedPriceXmlObj->addAttribute('excluding_tax', $formatedExclPrice);
                    $formatedPriceXmlObj->addAttribute('including_tax', $formatedInclPrice);
                } else {
                    if ($taxHelper->displaySalesPriceExclTax()) {
                        $priceXmlObj->addAttribute('regular', $exclPrice);
                        $formatedPriceXmlObj->addAttribute('regular', $formatedExclPrice);
                    }
                    if ($taxHelper->displaySalesPriceInclTax()) {
                        $priceXmlObj->addAttribute('regular', $inclPrice);
                        $formatedPriceXmlObj->addAttribute('regular', $formatedInclPrice);
                    }
                }

                /**
                 * Subtotal
                 */
                $exclPrice = $inclPrice = 0.00;
                if ($taxHelper->displaySalesPriceExclTax() || $taxHelper->displaySalesBothPrices()) {
                    if (Mage::helper('weee')->typeOfDisplay($item, array(
                                                                        0, 1, 4
                                                                   ), 'sales')
                        && $item->getWeeeTaxAppliedAmount()
                    ) {
                        $exclPrice = $item->getRowTotal()
                                     + $item->getWeeeTaxAppliedRowAmount()
                                     + $item->getWeeeTaxRowDisposition();
                    } else {
                        $exclPrice = $item->getRowTotal();
                    }
                }
                if ($taxHelper->displaySalesPriceInclTax() || $taxHelper->displaySalesBothPrices()) {
                    $_incl = $this->helper('checkout')->getSubtotalInclTax($item);
                    if (Mage::helper('weee')->typeOfDisplay($item, array(
                                                                        0, 1, 4
                                                                   ), 'sales')
                        && $item->getWeeeTaxAppliedAmount()
                    ) {
                        $inclPrice = $_incl + $item->getWeeeTaxAppliedRowAmount();
                    } else {
                        $inclPrice = $_incl - $item->getWeeeTaxRowDisposition();
                    }
                }
                $exclPrice = Mage::helper('xmlconnect')->formatPriceForXml($exclPrice);
                $formatedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);
                $inclPrice = Mage::helper('xmlconnect')->formatPriceForXml($inclPrice);
                $formatedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);
                $subtotalPriceXmlObj = $itemXml->addChild('subtotal');
                $subtotalFormatedPriceXmlObj = $itemXml->addChild('formated_subtotal');
                if ($taxHelper->displaySalesBothPrices()) {
                    $subtotalPriceXmlObj->addAttribute('excluding_tax', $exclPrice);
                    $subtotalPriceXmlObj->addAttribute('including_tax', $inclPrice);
                    $subtotalFormatedPriceXmlObj->addAttribute('excluding_tax', $formatedExclPrice);
                    $subtotalFormatedPriceXmlObj->addAttribute('including_tax', $formatedInclPrice);
                } else {
                    if ($taxHelper->displaySalesPriceExclTax()) {
                        $subtotalPriceXmlObj->addAttribute('regular', $exclPrice);
                        $subtotalFormatedPriceXmlObj->addAttribute('regular', $formatedExclPrice);
                    }
                    if ($taxHelper->displaySalesPriceInclTax()) {
                        $subtotalPriceXmlObj->addAttribute('regular', $inclPrice);
                        $subtotalFormatedPriceXmlObj->addAttribute('regular', $formatedInclPrice);
                    }
                }

                /**
                 * Options list
                 */
                if ($options = $renderer->getOptionList()) {
                    $itemOptionsXml = $itemXml->addChild('options');
                    foreach ($options as $option) {
                        $formatedOptionValue = $renderer->getFormatedOptionValue($option);
                        $optionXml = $itemOptionsXml->addChild('option');
                        $optionXml->addAttribute('label', $xmlObject->xmlentities(strip_tags($option['label'])));
                        $optionXml->addAttribute(
                            'text',
                            $xmlObject->xmlentities(strip_tags($formatedOptionValue['value']))
                        );
//                        if (isset($_formatedOptionValue['full_view'])) {
//                            $label = strip_tags($_option['label']);
//                            $value = strip_tags($_formatedOptionValue['full_view']);
//                        }
                    }
                }

//                /**
//                 * Item messages
//                 */
//                if ($messages = $renderer->getMessages()) {
//                    $itemMessagesXml = $itemXml->addChild('messages');
//                    foreach ($messages as $message) {
//                        $messageXml = $itemMessagesXml->addChild('option');
//                        $messageXml->addChild('type', $message['type']);
//                        $messageXml->addChild('text', $xmlObject->xmlentities(strip_tags($message['text'])));
//                    }
//                }
            }
        }
    }
}
