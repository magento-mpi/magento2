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
namespace Magento\Centinel\Controller\Adminhtml\Centinel;

class Index extends \Magento\Adminhtml\Controller\Action
{
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
                throw new \Exception('This payment method does not have centinel validation.');
            }
            $validator->reset();
            $this->_getPayment()->importData($paymentData);
            $result['authenticationUrl'] = $validator->getAuthenticationStartUrl();
        } catch (\Magento\Core\Exception $e) {
            $result['message'] = $e->getMessage();
        } catch (\Exception $e) {
            \Mage::logException($e);
            $result['message'] = __('Validation failed.');
        }
        $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
    }

    /**
     * Process autentication start action
     *
     */
    public function authenticationStartAction()
    {
        if ($validator = $this->_getValidator()) {
            \Mage::register('current_centinel_validator', $validator);
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
           if ($validator = $this->_getValidator()) {
                $request = $this->getRequest();

                $data = new \Magento\Object();
                $data->setTransactionId($request->getParam('MD'));
                $data->setPaResPayload($request->getParam('PaRes'));

                $validator->authenticate($data);
                \Mage::register('current_centinel_validator', $validator);
            }
        } catch (\Exception $e) {
            \Mage::register('current_centinel_validator', false);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Return payment model
     *
     * @return \Magento\Sales\Model\Quote\Payment
     */
    private function _getPayment()
    {
        $model = \Mage::getSingleton('Magento\Adminhtml\Model\Sales\Order\Create');
        return $model->getQuote()->getPayment();
    }

    /**
     * Return Centinel validation model
     *
     * @return \Magento\Centinel\Model\Service
     */
    private function _getValidator()
    {
        if ($this->_getPayment()->getMethodInstance()->getIsCentinelValidationEnabled()) {
            return $this->_getPayment()->getMethodInstance()->getCentinelValidator();
        }
        return false;
    }
}

