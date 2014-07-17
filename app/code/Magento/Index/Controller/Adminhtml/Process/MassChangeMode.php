<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Controller\Adminhtml\Process;

class MassChangeMode extends \Magento\Index\Controller\Adminhtml\Process
{
    /**
     * Mass change index mode of selected processes index
     *
     * @return void
     */
    public function execute()
    {
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->messageManager->addError(__('Please select Index(es)'));
        } else {
            try {
                $counter = 0;
                $mode = $this->getRequest()->getParam('index_mode');
                foreach ($processIds as $processId) {
                    /* @var $process \Magento\Index\Model\Process */
                    $process = $this->_processFactory->create()->load($processId);
                    if ($process->getId() && $process->getIndexer()->isVisible()) {
                        $process->setMode($mode)->save();
                        $counter++;
                    }
                }
                $this->messageManager->addSuccess(__('Total of %1 index(es) have changed index mode.', $counter));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('adminhtml/*/list');
    }
}
