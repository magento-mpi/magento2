<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Controller\Adminhtml\Process;

class ListAction extends \Magento\Index\Controller\Adminhtml\Process
{
    /**
     * Display processes grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Index Management'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Index::system_index');
        $this->_addContent($this->_view->getLayout()->createBlock('Magento\Index\Block\Adminhtml\Process'));
        $this->_view->renderLayout();
    }
}
