<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\PbridgePaypal\Controller\PbridgeIpn;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Pbridge ipn factory
     *
     * @var \Magento\PbridgePaypal\Model\Payment\Method\Pbridge\IpnFactory
     */
    protected $_pbridgeIpnFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\PbridgePaypal\Model\Payment\Method\Pbridge\IpnFactory $pbridgeIpnFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\PbridgePaypal\Model\Payment\Method\Pbridge\IpnFactory $pbridgeIpnFactory
    ) {
        $this->_pbridgeIpnFactory = $pbridgeIpnFactory;
        parent::__construct($context);
    }

    /**
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\PbridgePaypal\Model\Payment\Method\Pbridge\Ipn $ipn */
        $ipn = $this->_pbridgeIpnFactory->create();

        $ipn->setIpnFormData($this->getRequest()->getPost())->processIpnRequest();
        exit;
    }
}
