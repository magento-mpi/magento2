<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Controller_Index extends Mage_Core_Controller_Front_Action {

    function indexAction()
    {

    }

    /**
     * 404 not found action
     */
    public function notFoundAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHttpResponseCode(404);
        $this->getResponse()->setBody($this->__('Requested resource not found'));
    }
}
