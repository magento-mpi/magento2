<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleCheckout\Controller;

class Api extends \Magento\Core\Controller\Front\Action
{
    public function indexAction()
    {
        $res = \Mage::getModel('\Magento\GoogleCheckout\Model\Api')->processCallback();
        if ($res === false) {
            $this->_forward('noRoute');
        }
        else {
            exit;
        }
    }

    public function beaconAction()
    {
        \Mage::getModel('\Magento\GoogleCheckout\Model\Api')->debugData(array('request' => $_SERVER['QUERY_STRING'], 'dir' => 'in'));
    }
}
