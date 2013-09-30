<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unified IPN controller for all supported PayPal methods
 */
class Magento_Paypal_Controller_Ipn extends Magento_Core_Controller_Front_Action
{
    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Paypal_Model_IpnFactory
     */
    protected $_ipnFactory;

    /**
     * @var Magento_HTTP_Adapter_CurlFactory
     */
    protected $_curlFactory;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Paypal_Model_IpnFactory $ipnFactory
     * @param Magento_HTTP_Adapter_CurlFactory $curlFactory
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Paypal_Model_IpnFactory $ipnFactory,
        Magento_HTTP_Adapter_CurlFactory $curlFactory
    ) {
        $this->_logger = $context->getLogger();
        $this->_ipnFactory = $ipnFactory;
        $this->_curlFactory = $curlFactory;
        parent::__construct($context);
    }

    /**
     * Instantiate IPN model and pass IPN request to it
     */
    public function indexAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        try {
            $data = $this->getRequest()->getPost();
            $this->_ipnFactory->create()->processIpnRequest($data, $this->_curlFactory->create());
        } catch (Exception $e) {
            $this->_logger->logException($e);
        }
    }
}
