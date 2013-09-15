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
namespace Magento\Adminhtml\Block\Page;

class Header extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'page/header.phtml';

    /**
     * Backend data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData = null;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_backendData = $backendData;
        parent::__construct($coreData, $context, $data);
    }

    public function getHomeLink()
    {
        return $this->_backendData->getHomePageUrl();
    }

    public function getUser()
    {
        return \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser();
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
        return \Mage::getStoreConfig('web/browser_capabilities/javascript');
    }
}
