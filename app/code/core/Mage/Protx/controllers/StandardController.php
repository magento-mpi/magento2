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
 * @package    Mage_Protx
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Protx Form Method Front Controller
 *
 * @name       Mage_Protx_Form_Controller
 * @date       Fri Apr 04 15:46:14 EEST 2008
 */
class Mage_Protx_StandardController extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with protx strandard order transaction information
     *
     * @return Mage_Protx_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('protx/standard');
    }

    /**
     * When a customer chooses Protx on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setProtxStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('protx/standard_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * when paypal returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function  successAction()
    {
        $this->preResponse();

        $this->getStandard()->setResponseData($this->getRequest()->getQuery());
        $this->getStandard()->onSuccessResponse();
        $this->_redirect('checkout/onepage/success');
    }

    /**
     *  Failure response
     *
     *  @param    none
     *  @return	  void
     *  @date	  Mon Apr 07 21:40:53 EEST 2008
     */
    public function failureAction ()
    {
        $this->preResponse();

        $this->getStandard()->setResponseData($this->getRequest()->getQuery());
        $this->getStandard()->onFailureResponse();
        $this->_redirect('checkout/cart');
    }

    /**
     *  Pre actio
     *
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 20:54:40 EEST 2008
     */
    protected function preResponse ()
    {
        if (!$this->getRequest()->isGet()) {
            $this->_redirect('');
            return;
        }
        if($this->getStandard()->getDebug()){
            $debug = Mage::getModel('protx/api_debug')
                ->setApiEndpoint($this->getStandard()->getProtxUrl())
                ->setRequestBody(print_r($this->getRequest()->getQuery(),1))
                ->save();
        }
    }

}