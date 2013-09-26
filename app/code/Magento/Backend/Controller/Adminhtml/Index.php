<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Index backend controller
 */
class Magento_Backend_Controller_Adminhtml_Index extends Magento_Backend_Controller_ActionAbstract
{
    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $this->_redirect($this->_backendUrl->getStartupPageUrl());
    }
}
