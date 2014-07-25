<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Cache;

class Index extends \Magento\Backend\Controller\Adminhtml\Cache
{
    /**
     * Display cache management grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Cache Management'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::system_cache');
        $this->_view->renderLayout();
    }
}
