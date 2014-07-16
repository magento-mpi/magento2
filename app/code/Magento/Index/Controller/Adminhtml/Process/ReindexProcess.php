<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Controller\Adminhtml\Process;

class ReindexProcess extends \Magento\Index\Controller\Adminhtml\Process
{
    /**
     * Reindex all data what process is responsible
     *
     * @return void
     */
    public function execute()
    {
        /** @var $process \Magento\Index\Model\Process */
        $process = $this->_initProcess();
        if ($process) {
            try {
                \Magento\Framework\Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

                $process->reindexEverything();
                \Magento\Framework\Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $this->messageManager->addSuccess(__('%1 index was rebuilt.', $process->getIndexer()->getName()));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('There was a problem with reindexing process.'));
            }
        } else {
            $this->messageManager->addError(__('Cannot initialize the indexer process.'));
        }

        $this->_redirect('adminhtml/*/list');
    }
}
