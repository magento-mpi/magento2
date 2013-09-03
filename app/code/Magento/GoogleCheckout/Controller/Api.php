<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleCheckout_Controller_Api extends Magento_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $res = Mage::getModel('Magento_GoogleCheckout_Model_Api')->processCallback();
        if ($res === false) {
            $this->_forward('noRoute');
        }
        else {
            exit;
        }
    }

    public function beaconAction()
    {
        Mage::getModel('Magento_GoogleCheckout_Model_Api')->debugData(array('request' => $_SERVER['QUERY_STRING'], 'dir' => 'in'));
    }
}
