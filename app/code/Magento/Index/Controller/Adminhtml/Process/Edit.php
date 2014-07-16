<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Controller\Adminhtml\Process;

class Edit extends \Magento\Index\Controller\Adminhtml\Process
{
    /**
     * Process detail and edit action
     *
     * @return void
     */
    public function execute()
    {
        /** @var $process \Magento\Index\Model\Process */
        $process = $this->_initProcess();
        if ($process) {
            $this->_title->add($process->getIndexCode());
            $this->_title->add(__('System'));
            $this->_title->add(__('Index Management'));
            $this->_title->add(__($process->getIndexer()->getName()));

            $this->_coreRegistry->register('current_index_process', $process);
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            $this->messageManager->addError(__('Cannot initialize the indexer process.'));
            $this->_redirect('adminhtml/*/list');
        }
    }
}
