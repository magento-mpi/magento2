<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml header block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Page_Header extends Magento_Adminhtml_Block_Template
{
    protected $_template = 'page/header.phtml';

    public function getHomeLink()
    {
        return Mage::helper('Magento_Backend_Helper_Data')->getHomePageUrl();
    }

    public function getUser()
    {
        return Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser();
    }

    public function getLogoutLink()
    {
        return $this->getUrl('adminhtml/auth/logout');
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return $this->_storeConfig->getConfig('web/browser_capabilities/javascript');
    }

}
