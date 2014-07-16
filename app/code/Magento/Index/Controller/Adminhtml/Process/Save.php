<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Controller\Adminhtml\Process;

class Save extends \Magento\Index\Controller\Adminhtml\Process
{
    /**
     * Save process data
     *
     * @return void
     */
    public function execute()
    {
        /** @var $process \Magento\Index\Model\Process */
        $process = $this->_initProcess();
        if ($process) {
            $mode = $this->getRequest()->getPost('mode');
            if ($mode) {
                $process->setMode($mode);
            }
            try {
                $process->save();
                $this->messageManager->addSuccess(__('The index has been saved.'));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('There was a problem with saving process.'));
            }
            $this->_redirect('adminhtml/*/list');
        } else {
            $this->messageManager->addError(__('Cannot initialize the indexer process.'));
            $this->_redirect('adminhtml/*/list');
        }
    }
}
