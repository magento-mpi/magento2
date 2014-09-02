<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Controller\Adminhtml\Indexer;

class ListAction extends \Magento\Indexer\Controller\Adminhtml\Indexer
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
        $this->_setActiveMenu('Magento_Indexer::system_index');
        $this->_view->renderLayout();
    }
}
