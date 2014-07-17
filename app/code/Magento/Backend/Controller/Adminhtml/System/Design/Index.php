<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Design;

class Index extends \Magento\Backend\Controller\Adminhtml\System\Design
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Store Design'));
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::system_design_schedule');
        $this->_view->renderLayout();
    }
}
