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
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Controller_Adminhtml_Index extends Magento_Backend_Controller_ActionAbstract
{
    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $this->_redirect(Mage::getSingleton('Magento_Backend_Model_Url')->getStartupPageUrl());
    }
}
