<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Controller\Adminhtml\Locks;

class Index extends \Magento\Pci\Controller\Adminhtml\Locks
{
    /**
     * Render page with grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_acl_locks');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Locked Users'));
        $this->_view->renderLayout();
    }
}
