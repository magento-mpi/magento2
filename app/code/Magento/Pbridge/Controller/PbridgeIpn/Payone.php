<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\PbridgeIpn;

class Payone extends \Magento\Framework\App\Action\Action
{
    /**
     * Payone IPN factory
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Payone\IpnFactory
     */
    protected $_payoneIpnFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Pbridge\Model\Payment\Method\Payone\IpnFactory $payoneIpnFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Pbridge\Model\Payment\Method\Payone\IpnFactory $payoneIpnFactory
    ) {
        $this->_payoneIpnFactory = $payoneIpnFactory;
        parent::__construct($context);
    }

    /**
     * Payone IPN action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Pbridge\Model\Payment\Method\Payone\Ipn $ipn */
        $ipn = $this->_pbridgeIpnFactory->create();
        $ipn->setIpnFormData($this->getRequest()->getPost())->processIpnRequest();
        exit;
    }
}
