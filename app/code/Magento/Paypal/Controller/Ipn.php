<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller;

/**
 * Unified IPN controller for all supported PayPal methods
 */
class Ipn extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Logger
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
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Paypal\Model\IpnFactory $ipnFactory
     * @param \Magento\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Paypal\Model\IpnFactory $ipnFactory,
        \Magento\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Logger $logger
    ) {
        $this->_logger = $logger;
        $this->_ipnFactory = $ipnFactory;
        $this->_curlFactory = $curlFactory;
        parent::__construct($context);
    }

    /**
     * Instantiate IPN model and pass IPN request to it
     *
     * @return void
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
