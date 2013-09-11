<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirtectPost Payment Controller
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Controller\Directpost;

class Payment extends \Magento\Core\Controller\Front\Action
{
    /**
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session');
    }

    /**
     * Get session model

     * @return \Magento\Authorizenet\Model\Directpost\Session
     */
    protected function _getDirectPostSession()
    {
        return \Mage::getSingleton('Magento\Authorizenet\Model\Directpost\Session');
    }

    /**
     * Response action.
     * Action for Authorize.net SIM Relay Request.
     */
    public function responseAction()
    {
        $params = array();
        $data = $this->getRequest()->getPost();
        /* @var $paymentMethod Magento_Authorizenet_Model_DirectPost */
        $paymentMethod = \Mage::getModel('Magento\Authorizenet\Model\Directpost');

        $result = array();
        if (!empty($data['x_invoice_num'])) {
            $result['x_invoice_num'] = $data['x_invoice_num'];
        }

        try {
            if (!empty($data['store_id'])) {
                $paymentMethod->setStore($data['store_id']);
            }
            $paymentMethod->process($data);
            $result['success'] = 1;
        }
        catch (\Magento\Core\Exception $e) {
            \Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = $e->getMessage();
        }
        catch (\Exception $e) {
            \Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = __('We couldn\'t process your order right now. Please try again later.');
        }

        if (!empty($data['controller_action_name'])
            && strpos($data['controller_action_name'], 'sales_order_') === false
        ) {
            if (!empty($data['key'])) {
                $result['key'] = $data['key'];
            }
            $result['controller_action_name'] = $data['controller_action_name'];
            $result['is_secure'] = isset($data['is_secure']) ? $data['is_secure'] : false;
            $params['redirect'] = \Mage::helper('Magento\Authorizenet\Helper\Data')->getRedirectIframeUrl($result);
        }

        \Mage::register('authorizenet_directpost_form_params', $params);
        $this->addPageLayoutHandles();
        $this->loadLayout(false)->renderLayout();
    }

    /**
     * Retrieve params and put javascript into iframe
     *
     */
    public function redirectAction()
    {
        $redirectParams = $this->getRequest()->getParams();
        $params = array();
        if (!empty($redirectParams['success'])
            && isset($redirectParams['x_invoice_num'])
            && isset($redirectParams['controller_action_name'])
        ) {
            $this->_getDirectPostSession()->unsetData('quote_id');
            $params['redirect_parent'] = \Mage::helper('Magento\Authorizenet\Helper\Data')->getSuccessOrderUrl($redirectParams);
        }
        if (!empty($redirectParams['error_msg'])) {
            $cancelOrder = empty($redirectParams['x_invoice_num']);
            $this->_returnCustomerQuote($cancelOrder, $redirectParams['error_msg']);
        }

        if (isset($redirectParams['controller_action_name'])
            && strpos($redirectParams['controller_action_name'], 'sales_order_') !== false
        ) {
            unset($redirectParams['controller_action_name']);
            unset($params['redirect_parent']);
        }

        \Mage::register('authorizenet_directpost_form_params', array_merge($params, $redirectParams));
        $this->addPageLayoutHandles();
        $this->loadLayout(false)->renderLayout();
    }

    /**
     * Send request to authorize.net
     *
     */
    public function placeAction()
    {
        $paymentParam = $this->getRequest()->getParam('payment');
        $controller = $this->getRequest()->getParam('controller');
        if (isset($paymentParam['method'])) {
            $params = \Mage::helper('Magento\Authorizenet\Helper\Data')->getSaveOrderUrlParams($controller);
            $this->_getDirectPostSession()->setQuoteId($this->_getCheckout()->getQuote()->getId());
            $this->_forward(
                $params['action'],
                $params['controller'],
                $params['module'],
                $this->getRequest()->getParams()
            );
        } else {
            $result = array(
                'error_messages' => __('Please choose a payment method.'),
                'goto_section'   => 'payment'
            );
            $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }

    /**
     * Return customer quote by ajax
     *
     */
    public function returnQuoteAction()
    {
        $this->_returnCustomerQuote();
        $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode(array('success' => 1)));
    }

    /**
     * Return customer quote
     *
     * @param bool $cancelOrder
     * @param string $errorMsg
     */
    protected function _returnCustomerQuote($cancelOrder = false, $errorMsg = '')
    {
        $incrementId = $this->_getDirectPostSession()->getLastOrderIncrementId();
        if ($incrementId &&
            $this->_getDirectPostSession()
                ->isCheckoutOrderIncrementIdExist($incrementId)
        ) {
            /* @var $order \Magento\Sales\Model\Order */
            $order = \Mage::getModel('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                $quote = \Mage::getModel('Magento\Sales\Model\Quote')
                    ->load($order->getQuoteId());
                if ($quote->getId()) {
                    $quote->setIsActive(1)
                        ->setReservedOrderId(NULL)
                        ->save();
                    $this->_getCheckout()->replaceQuote($quote);
                }
                $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($incrementId);
                $this->_getDirectPostSession()->unsetData('quote_id');
                if ($cancelOrder) {
                    $order->registerCancellation($errorMsg)->save();
                }
            }
        }
    }
}
