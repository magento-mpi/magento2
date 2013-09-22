<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unified IPN controller for all supported PayPal methods
 */
namespace Magento\Paypal\Controller;

class Ipn extends \Magento\Core\Controller\Front\Action
{
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
            \Mage::getModel('Magento_Paypal_Model_Ipn')->processIpnRequest($data, new Magento_HTTP_Adapter_Curl());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
    }
}
