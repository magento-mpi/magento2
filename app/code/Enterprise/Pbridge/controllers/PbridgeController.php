<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Index controller
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_PbridgeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Load only action layout handles
     *
     * @return Enterprise_Pbridge_PbridgeController
     */
    protected function _initActionLayout()
    {
        $this->addActionLayoutHandles();
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;
        return $this;
    }

    /**
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('result');
    }

    /**
     * Iframe Ajax Action
     *
     *  @return void
     */
    public function iframeAction()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = Mage::helper('Mage_Payment_Helper_Data')->getMethodInstance($methodCode);
            if ($methodInstance) {
                $block = $this->getLayout()->createBlock($methodInstance->getFormBlockType());
                $block->setMethod($methodInstance);
                if($this->getRequest()->getParam('data')) {
                    $block->setFormParams($this->getRequest()->getParam('data', null));
                }
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            Mage::throwException(Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Payment Method Code is not passed.'));
        }
    }

    /**
     * Iframe Ajax Action for review page
     *
     *  @return void
     */
    public function reviewAction()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = Mage::helper('Mage_Payment_Helper_Data')->getMethodInstance($methodCode);
            if ($methodInstance) {
                $block = $this->getLayout()->createBlock('Enterprise_Pbridge_Block_Checkout_Payment_Review_Iframe');
                $block->setMethod($methodInstance);
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            Mage::throwException(Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Payment Method Code is not passed.'));
        }
    }

    /**
     * Review success action
     *
     *  @return void
     */
    public function successAction()
    {
        $this->_initActionLayout();
        $this->renderLayout();
    }

    /**
     * Redirect to Onepage checkout success page
     *
     *  @return void
     */
    public function onepagesuccessAction()
    {
        $this->_initActionLayout();
        $this->renderLayout();
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }
    /**
     * Review success action
     *
     *  @return void
     */
    public function cancelAction()
    {
        try {
            // if there is an order - cancel it
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            $order = ($orderId) ? Mage::getModel('Mage_Sales_Model_Order')->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess($this->__('Order has been canceled.'))
                ;
            } else {
                $this->_getCheckoutSession()->addSuccess($this->__('Order has been canceled.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to cancel order.'));
            Mage::logException($e);
        }

        $this->_initActionLayout();
        $this->renderLayout();
    }

    /**
     * Review error action
     *
     *  @return void
     */
    public function errorAction()
    {
        $this->_initActionLayout();
        $this->renderLayout();
    }

    /**
     * Result Action
     *
     * @return void
     */
    public function resultAction()
    {
        $this->_initActionLayout();
        $this->renderLayout();
    }

    /**
     * Validate all agreements
     * (terms and conditions are agreed)
     */
    public function validateAgreementAction()
    {
        $result = array();
        $result['success'] = true;
        $requiredAgreements = Mage::helper('Mage_Checkout_Helper_Data')->getRequiredAgreementIds();
        if ($requiredAgreements) {
            $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
            $diff = array_diff($requiredAgreements, $postedAgreements);
            if ($diff) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
            }
        }
        $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
    }
}
