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
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function indexAction()
    {
        $ipn = \Mage::getModel('\Magento\Pbridge\Model\Payment\Method\Pbridge\Ipn');

        $ipn->setIpnFormData($this->getRequest()->getPost())
            ->processIpnRequest();
        exit;
    }

}
