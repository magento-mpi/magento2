<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Controller;

use \Magento\Paypal\UnavailableException;

/**
 * Unified IPN controller for all supported PayPal methods
 */
class Ipn extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Paypal\Model\IpnFactory
     */
    protected $_ipnFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Paypal\Model\IpnFactory $ipnFactory
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Paypal\Model\IpnFactory $ipnFactory,
        \Magento\Framework\Logger $logger
    ) {
        $this->_logger = $logger;
        $this->_ipnFactory = $ipnFactory;
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
            $this->_ipnFactory->create(array('data' => $data))->processIpnRequest();
        } catch (UnavailableException $e) {
            $this->_logger->logException($e);
            $this->getResponse()->setHeader('HTTP/1.1', '503 Service Unavailable')->sendResponse();
            /** @todo eliminate usage of exit statement */
            exit;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
}
