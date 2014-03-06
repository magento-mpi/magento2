<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ipn controller
 */
namespace Magento\PbridgePaypal\Controller;

class PbridgeIpn extends \Magento\App\Action\Action
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
     * @param \Magento\App\Action\Context $context
     * @param \Magento\PbridgePaypal\Model\Payment\Method\Pbridge\IpnFactory $pbridgeIpnFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
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
    public function indexAction()
    {
        /** @var \Magento\PbridgePaypal\Model\Payment\Method\Pbridge\Ipn $ipn */
        $ipn = $this->_pbridgeIpnFactory->create();

        $ipn->setIpnFormData($this->getRequest()->getPost())
            ->processIpnRequest();
        exit;
    }
}
