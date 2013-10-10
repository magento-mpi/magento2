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
namespace Magento\Paypal\Controller;

class Ipn extends \Magento\Core\Controller\Front\Action
{
    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Paypal\Model\IpnFactory
     */
    protected $_ipnFactory;

    /**
     * @var \Magento\HTTP\Adapter\CurlFactory
     */
    protected $_curlFactory;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Paypal\Model\IpnFactory $ipnFactory
     * @param \Magento\HTTP\Adapter\CurlFactory $curlFactory
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Paypal\Model\IpnFactory $ipnFactory,
        \Magento\HTTP\Adapter\CurlFactory $curlFactory
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
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
    }
}
