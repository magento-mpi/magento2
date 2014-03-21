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
 * Adminhtml header notices block
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Page;

class Notices extends \Magento\Backend\Block\Template
{

    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return $this->_storeConfig->getValue('web/browser_capabilities/javascript', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Check if demo store notice should be displayed
     *
     * @return boolean
     */
    public function displayDemoNotice()
    {
        return $this->_storeConfig->getValue('design/head/demonotice', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

}
