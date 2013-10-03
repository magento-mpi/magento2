<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Index controller
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Controller;

class PbridgeIpn extends \Magento\Core\Controller\Front\Action
{
    /**
     * Pbridge ipn factory
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge\IpnFactory
     */
    protected $_pbridgeIpnFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Pbridge\Model\Payment\Method\Pbridge\IpnFactory $pbridgeIpnFactory
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Pbridge\Model\Payment\Method\Pbridge\IpnFactory $pbridgeIpnFactory
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
        /** @var \Magento\Pbridge\Model\Payment\Method\Pbridge\Ipn $ipn */
        $ipn = $this->_pbridgeIpnFactory->create();

        $ipn->setIpnFormData($this->getRequest()->getPost())
            ->processIpnRequest();
        exit;
    }

}
