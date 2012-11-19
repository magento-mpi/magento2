<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_IndexController extends Mage_Core_Controller_Front_Action {

    function indexAction()
    {

    }

    /**
     * 404 missed file action
     */
    public function fileMissedAction()
    {
        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('Mage_Core_Helper_Data');
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHttpResponseCode(404);
        $this->getResponse()->setBody($helper->__('Requested view file not found'));
    }
}
