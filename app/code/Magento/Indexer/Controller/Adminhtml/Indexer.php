<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Controller\Adminhtml;

class Indexer extends \Magento\Backend\App\Action
{
    /**
     * Display processes grid action
     */
    public function listAction()
    {
        $this->_title->add(__('New Index Management'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Indexer::system_index');
        $this->_view->renderLayout();
    }

    /**
     * Turn mview off for the given indexers
     */
    public function massOnTheFlyAction()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexer_id) {
                    /** @var \Magento\Indexer\Model\IndexerInterface $model */
                    $model = $this->_objectManager->create('Magento\Indexer\Model\IndexerInterface')
                        ->load($indexer_id);
                    $model->setScheduled(false);
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 indexer(s) have been turned Update on Save mode on.', count($indexerIds))
                );
            } catch (\Magento\Core\Exception $e) {
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

    /**
     * Turn mview on for the given indexers
     */
    public function massChangelogAction()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexer_id) {
                    /** @var \Magento\Indexer\Model\IndexerInterface $model */
                    $model = $this->_objectManager->create('Magento\Indexer\Model\IndexerInterface')
                        ->load($indexer_id);
                    $model->setScheduled(true);
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 indexer(s) have been turned Update by Schedule mode on.', count($indexerIds))
                );
            } catch (\Magento\Core\Exception $e) {
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

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->_request->getActionName()) {
            case 'list':
                return $this->_authorization->isAllowed('Magento_Indexer::index');
            case 'massOnTheFly':
            case 'massChangelog':
                return $this->_authorization->isAllowed('Magento_Indexer::changeMode');
        }
        return false;
    }
}
