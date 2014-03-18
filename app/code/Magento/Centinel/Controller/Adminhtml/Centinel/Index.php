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

class Index extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Process validate payment data action
     *
     * @return void
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
        } catch (\Magento\Model\Exception $e) {
            $result['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $result['message'] = __('Validation failed.');
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }

    /**
     * Process autentication start action
     *
     * @return void
     */
    public function authenticationStartAction()
    {
        $validator = $this->_getValidator();
        if ($validator) {
            $this->_coreRegistry->register('current_centinel_validator', $validator);
        }
        $this->_view->loadLayout()->renderLayout();
    }

    /**
     * Process autentication complete action
     *
     * @return void
     */
    public function authenticationCompleteAction()
    {
        try {
            $validator = $this->_getValidator();
            if ($validator) {
                $request = $this->getRequest();

                $data = new \Magento\Object();
                $data->setTransactionId($request->getParam('MD'));
                $data->setPaResPayload($request->getParam('PaRes'));

                $validator->authenticate($data);
                $this->_coreRegistry->register('current_centinel_validator', $validator);
            }
        } catch (\Exception $e) {
            $this->_coreRegistry->register('current_centinel_validator', false);
        }
        $this->_view->loadLayout()->renderLayout();
    }

    /**
     * Return payment model
     *
     * @return \Magento\Sales\Model\Quote\Payment
     */
    private function _getPayment()
    {
        return $this->_objectManager->get('Magento\Sales\Model\AdminOrder\Create')->getQuote()->getPayment();
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
