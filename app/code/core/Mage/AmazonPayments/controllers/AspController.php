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
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_AmazonPayments_AspController extends Mage_Core_Controller_Front_Action
{
  
    public function getPayment()
    {
        return Mage::getSingleton('amazonpayments/payment_asp');
    }

    public function getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    // PAY
    
    public function payAction()
    {
        $session = $this->getSession();
        $session->setAmazonAspQuoteId($session->getQuoteId());
        $session->setAmazonAspLastRealOrderId($session->getLastRealOrderId());
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());

        $payment = $this->getPayment(); 
        $payment->setOrder($order);
        
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('amazonpayments/asp_redirect')
                ->setRedirectUrl($payment->getPayRedirectUrl())
                ->setRedirectParams($payment->getPayRedirectParams())
                ->toHtml()
         );
        
        $payment->processEventRedirect();       
                
        $session->unsQuoteId();
        $session->unsLastRealOrderId();
    }
    
    public function returnSuccessAction()
    {   
        $session = $this->getSession();
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getAmazonAspLastRealOrderId());
        if ($order->isEmpty()) {
            return false;
        }
        
        $payment = $this->getPayment(); 
        $payment->setOrder($order);
        $payment->processEventReturnSuccess();
        
        $session->setQuoteId($session->getAmazonAspQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();
        $session->setLastRealOrderId($session->getAmazonAspLastRealOrderId(true));
        $this->_redirect('checkout/onepage/success');
    }

    public function returnCancelAction()
    {
        $session = $this->getSession();
        $session->setQuoteId($session->getAmazonAspQuoteId(true));
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getAmazonAspLastRealOrderId());
        if ($order->isEmpty()) {
            return false;
        }

        $payment = $this->getPayment(); 
        $payment->setOrder($order);
        $payment->processEventReturnCancel();
                
        $this->_redirect('checkout/cart/');
    }

    // NOTIFICATION
    
    public function notificationAction()
    {
				        //BEGIN DEBUG
				        if (0) {
	                        $tmp = array();
	                        foreach ($_POST as $kay => $value) {
	                            $tmp[] = $kay . '=' . $value;
	                        }
	                        $Q = $_SERVER['REQUEST_URI'] . '?' . implode('&', $tmp);
				        	
                            $fp = fopen('./var/ipn_debug/ipn.txt',"a");
					        fwrite($fp, "ip=" . $_SERVER['REMOTE_ADDR'] . "\n");
					        fwrite($fp, "uri=" . $_SERVER['REQUEST_URI'] . "\n");
					        fwrite($fp, "uri_POST=" . $Q . "\n");
					        fwrite($fp, "referer=" . getenv("HTTP_REFERER") . "\n");
					        foreach ($this->getRequest()->getParams() as $kay => $value) {
					            fwrite($fp, "$kay=$value" . "\n");
					        }
					        fwrite($fp, "\n");
					        fclose($fp);
				        }
				        //END DEBUG

        $this->getPayment()->processNotification($this->getRequest()->getParams());
    }
    
    
    
    
    
    
    
    
    
    
    
    /**
     * when customer select eWay payment method
     */
    public function checkoutAction()
    {
        $session = Mage::getSingleton('checkout/session');         
        $session->setStepData('billing', array(
            'label'     => 'Shipping Method XXXXXXX',
            'is_show'   => '1'
        ));
        
        $this->_redirect('checkout');
    }
    
    
    

    
    

    public function fpsAction() {

    	$fps = Mage::getSingleton('amazonpayments/api_asp')->getFps();
    	
    	$amount = Mage::getSingleton('amazonpayments/api_asp_amount')
            ->setValue('2.50')
            ->setCurrencyCode('USD');
    	$request = $fps->getRequest(Mage_AmazonPayments_Model_Api_Asp_Fps::ACTION_CODE_REFUND)
            ->setTransactionId('13UK9AHGZ5D91UQ4152ZZZUJCZSANLU765O')
            ->setReferenceId('#3')
            ->setAmount($amount)
            ->setDescription('#2_DESC');
        

        /*$amount = Mage::getSingleton('amazonpayments/api_asp_amount')
            ->setValue('90.50')
            ->setCurrencyCode('USD');
        $request = $fps->getRequest(Mage_AmazonPayments_Model_Api_Asp_Fps::ACTION_CODE_SETTLE)
            ->setTransactionId('13UK9AHGZ5D91UQ4152ZZZUJCZSANLU765O')
            ->setAmount($amount);
        */
            
        /*$request = $fps->getRequest(Mage_AmazonPayments_Model_Api_Asp_Fps::ACTION_CODE_CANCEL)
            ->setTransactionId('13UKAPTN74NROOMGSLA1C8TGI4SH77MGCK5')
            ->setDescription('CANCEL ### 1');
*/
        $response = $fps->process($request);    
        /*pr*/if(1){echo('<div style="border:1px solid #000">'.__FILE__.':'.__LINE__.'<pre>');
        print_r($response);  echo('</pre></div>');}
        die('OK');
        
    	

    }

}