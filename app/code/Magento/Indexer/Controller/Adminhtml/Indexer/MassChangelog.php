<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Controller\Adminhtml\Indexer;

class MassChangelog extends \Magento\Indexer\Controller\Adminhtml\Indexer
{
    /**
     * Turn mview on for the given indexers
     *
     * @return void
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexer_id) {
                    /** @var \Magento\Indexer\Model\IndexerInterface $model */
                    $model = $this->_objectManager->create(
                        'Magento\Indexer\Model\IndexerInterface'
                    )->load(
                        $indexer_id
                    );
                    $model->setScheduled(true);
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 indexer(s) have been turned Update by Schedule mode on.', count($indexerIds))
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("We couldn't change indexer(s)' mode because of an error.")
                );
            }
        }
        $this->_redirect('*/*/list');
    }
}
