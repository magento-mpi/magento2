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
 * @package     Mage_DirectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_DirectPayment_PaygateController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getModel('checkout/cart');
    }
    
    public function placeAction()
    {
        Mage::log($this->getRequest());
    }
    
    public function cancelAction()
    {
        $orderId = $this->getRequest()->getPost('orderId');
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $quoteId = $order->getQuoteId();
            $order->cancel()
                    ->save();
            $quote = Mage::getModel('sales/quote')
                        ->load($quoteId)
                        ->setIsActive(1)
                        ->setReservedOrderId(NULL)
                        ->save();
            Mage::getSingleton('checkout/session')->replaceQuote($quote);
            $result['success'] = 1;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}
