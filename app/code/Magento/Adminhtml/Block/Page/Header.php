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
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
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
        return $this->_storeConfig->getConfig('web/browser_capabilities/javascript');
    }
}
