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
 * @category   Mage
 * @package    Mage_Eway
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eway Sewcure Checkout Controller
 */
class Mage_Eway_SecureController extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     *
     * @return Mage_Eway_Model_Standard
     */
    public function getModel()
    {
        return Mage::getSingleton('eway/secure');
    }

    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setEwayQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('eway/secure_redirect')->toHtml());
        $session->unsQuoteId();
    }

    public function  successAction()
    {
        $this->_checkReturnedPost();
        
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getEwayQuoteId(true));

        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        $order = Mage::getModel('sales/order');

        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        if($order->getId()){
            $order->sendNewOrderEmail();
        }

        $this->_redirect('checkout/onepage/success');
    }
    
    protected function _checkReturnedPost()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('');
            return;
        }

        if($this->getModel()->getDebug()){
            $debug = Mage::getModel('eway/api_debug')
                ->setResponseBody(print_r($this->getRequest()->getPost(),1))
                ->save();
        }

        $this->getModel()->setFormData($this->getRequest()->getPost());
        $this->getModel()->postSubmit();
    }

}
