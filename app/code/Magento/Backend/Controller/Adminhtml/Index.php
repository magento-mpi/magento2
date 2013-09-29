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
namespace Magento\Backend\Controller\Adminhtml;

class Index extends \Magento\Backend\Controller\AbstractAction
{
    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $this->_redirect(\Mage::getSingleton('Magento\Backend\Model\Url')->getStartupPageUrl());
    }
}
