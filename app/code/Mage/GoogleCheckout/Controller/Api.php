<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_GoogleCheckout_Controller_Api extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $res = Mage::getModel('Mage_GoogleCheckout_Model_Api')->processCallback();
        if ($res === false) {
            $this->_forward('noRoute');
        }
        else {
            exit;
        }
    }

    public function beaconAction()
    {
        Mage::getModel('Mage_GoogleCheckout_Model_Api')->debugData(array('request' => $_SERVER['QUERY_STRING'], 'dir' => 'in'));
    }
}
