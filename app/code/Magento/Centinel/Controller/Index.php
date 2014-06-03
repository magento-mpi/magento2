<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Centinel Authenticate Controller
 *
 */
namespace Magento\Centinel\Controller;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
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

                $data = new \Magento\Framework\Object();
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
        return $this->_objectManager->get('Magento\Checkout\Model\Session')->getQuote()->getPayment();
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
