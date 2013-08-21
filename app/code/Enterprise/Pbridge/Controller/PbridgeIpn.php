<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Index controller
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Controller_PbridgeIpn extends Magento_Core_Controller_Front_Action
{
    /**
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function indexAction()
    {
        $ipn = Mage::getModel('Enterprise_Pbridge_Model_Payment_Method_Pbridge_Ipn');

        $ipn->setIpnFormData($this->getRequest()->getPost())
            ->processIpnRequest();
        exit;
    }

}
