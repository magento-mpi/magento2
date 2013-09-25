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
class Magento_Pbridge_Controller_PbridgeIpn extends Magento_Core_Controller_Front_Action
{
    /**
     * Pbridge ipn factory
     *
     * @var Magento_Pbridge_Model_Payment_Method_Pbridge_IpnFactory
     */
    protected $_pbridgeIpnFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Pbridge_Model_Payment_Method_Pbridge_IpnFactory $pbridgeIpnFactory
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Pbridge_Model_Payment_Method_Pbridge_IpnFactory $pbridgeIpnFactory
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
        /** @var Magento_Pbridge_Model_Payment_Method_Pbridge_Ipn $ipn */
        $ipn = $this->_pbridgeIpnFactory->create();

        $ipn->setIpnFormData($this->getRequest()->getPost())
            ->processIpnRequest();
        exit;
    }

}
