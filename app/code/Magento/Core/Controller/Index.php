<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Core_Controller_Index extends Magento_Core_Controller_Front_Action {

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
        $this->getResponse()->setBody(__('Requested resource not found'));
    }
}
