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
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * Place order action.
     * Action for Authorize.net SIM Relay Request.
     */
    public function placeAction()
    {
        Mage::log($this->getRequest()->getParams());
       
        $this->getResponse()->setBody(
        	'<html><head><script language="javascript">
            <!--
            window.parent.directPayment.isResponse = true;
            //-->
            </script>
            </head><body></body></html>'
        );
    }
    
    /**
     * Cancel wrong order and return quote to customer.
     */
    public function cancelAction()
    {
        $orderId = $this->getRequest()->getPost('orderId');
        $result = array();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()){
                $orderIds = $this->_getCheckout()->setDirectPostOrderIds();
                if (is_array($orderIds) && !empty($orderIds[$order->getId()])){
            
                    //check if order exists and assigned to
                    $quoteId = $order->getQuoteId();
                    $order->cancel()
                        ->save();
                    if ($quoteId){
                        $quote = Mage::getModel('sales/quote')
                            ->load($quoteId);
                        if ($quote->getId()){
                            $quote->setIsActive(1)
                                ->setReservedOrderId(NULL)
                                ->save();
                            $this->_getCheckout()->replaceQuote($quote);
                            $result['success'] = 1;
                        }
                    }
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
