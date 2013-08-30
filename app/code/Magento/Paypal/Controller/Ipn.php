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
class Magento_Paypal_Controller_Ipn extends Magento_Core_Controller_Front_Action
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
            Mage::getModel('Magento_Paypal_Model_Ipn')->processIpnRequest($data, new Magento_HTTP_Adapter_Curl());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
