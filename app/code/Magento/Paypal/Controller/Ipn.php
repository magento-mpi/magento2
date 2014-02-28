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
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Paypal\Model\IpnFactory $ipnFactory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Paypal\Model\IpnFactory $ipnFactory,
        \Magento\Logger $logger
    ) {
        $this->_logger = $logger;
        $this->_ipnFactory = $ipnFactory;
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
            $this->_ipnFactory->create(array('data' => $data))->processIpnRequest();
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
    }
}
