<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Centinel Index Controller
 *
 * @category Magento
 * @package  Magento_Centinel
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Magento_Centinel_Controller_Adminhtml_Centinel_Index extends Magento_Adminhtml_Controller_Action
{
    /**
     * Cart
     *
     * @var Magento_Checkout_Model_Cart_Interface
     */
    protected $_checkoutCart;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Checkout_Model_Cart_Interface $checkoutCart
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Checkout_Model_Cart_Interface $checkoutCart,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_checkoutCart = $checkoutCart;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Process validate payment data action
     *
     */
    public function validatePaymentDataAction()
    {
        $result = array();
        try {
            $paymentData = $this->getRequest()->getParam('payment');
            $validator = $this->_getValidator();
            if (!$validator) {
                throw new Exception('This payment method does not have centinel validation.');
            }
            $validator->reset();
            $this->_getPayment()->importData($paymentData);
            $result['authenticationUrl'] = $validator->getAuthenticationStartUrl();
        } catch (Magento_Core_Exception $e) {
            $result['message'] = $e->getMessage();
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $result['message'] = __('Validation failed.');
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Process autentication start action
     *
     */
    public function authenticationStartAction()
    {
        $validator = $this->_getValidator();
        if ($validator) {
            $this->_coreRegistry->register('current_centinel_validator', $validator);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Process autentication complete action
     *
     */
    public function authenticationCompleteAction()
    {
        try {
            $validator = $this->_getValidator();
            if ($validator) {
                $request = $this->getRequest();

                $data = new Magento_Object();
                $data->setTransactionId($request->getParam('MD'));
                $data->setPaResPayload($request->getParam('PaRes'));

                $validator->authenticate($data);
                $this->_coreRegistry->register('current_centinel_validator', $validator);
            }
        } catch (Exception $e) {
            $this->_coreRegistry->register('current_centinel_validator', false);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Return payment model
     *
     * @return Magento_Sales_Model_Quote_Payment
     */
    private function _getPayment()
    {
        return $this->_checkoutCart->getQuote()->getPayment();
    }

    /**
     * Return Centinel validation model
     *
     * @return Magento_Centinel_Model_Service
     */
    private function _getValidator()
    {
        if ($this->_getPayment()->getMethodInstance()->getIsCentinelValidationEnabled()) {
            return $this->_getPayment()->getMethodInstance()->getCentinelValidator();
        }
        return false;
    }
}
