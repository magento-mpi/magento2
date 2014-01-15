<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Process extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Index\Model\ProcessFactory
     */
    protected $_processFactory;

    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Index\Model\ProcessFactory $processFactory
     * @param \Magento\Index\Model\Indexer $indexer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Index\Model\ProcessFactory $processFactory,
        \Magento\Index\Model\Indexer $indexer
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_processFactory = $processFactory;
        $this->_indexer = $indexer;
        parent::__construct($context);
    }

    /**
     * Initialize process object by request
     *
     * @return \Magento\Index\Model\Process|false
     */
    protected function _initProcess()
    {
        $processId = $this->getRequest()->getParam('process');
        if ($processId) {
            /** @var $process \Magento\Index\Model\Process */
            $process = $this->_processFactory->create()->load($processId);
            if ($process->getId() && $process->getIndexer()->isVisible()) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Display processes grid action
     */
    public function listAction()
    {
        $this->_title->add(__('Index Management'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Index::system_index');
        $this->_addContent($this->_view->getLayout()->createBlock('Magento\Index\Block\Adminhtml\Process'));
        $this->_view->renderLayout();
    }

    /**
     * Process detail and edit action
     */
    public function editAction()
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
            $this->messageManager->addError(
                __('Cannot initialize the indexer process.')
            );
            $this->_redirect('adminhtml/*/list');
        }
    }

    /**
     * Save process data
     */
    public function saveAction()
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
                $this->messageManager->addSuccess(
                    __('The index has been saved.')
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                     __('There was a problem with saving process.')
                );
            }
            $this->_redirect('adminhtml/*/list');
        } else {
            $this->messageManager->addError(
                __('Cannot initialize the indexer process.')
            );
            $this->_redirect('adminhtml/*/list');
        }
    }

    /**
     * Reindex all data what process is responsible
     */
    public function reindexProcessAction()
    {
        /** @var $process \Magento\Index\Model\Process */
        $process = $this->_initProcess();
        if ($process) {
            try {
                \Magento\Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

                $process->reindexEverything();
                \Magento\Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $this->messageManager->addSuccess(
                    __('%1 index was rebuilt.', $process->getIndexer()->getName())
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                     __('There was a problem with reindexing process.')
                );
            }
        } else {
            $this->messageManager->addError(
                __('Cannot initialize the indexer process.')
            );
        }

        $this->_redirect('adminhtml/*/list');
    }

    /**
     * Reindex pending events for index process
     */
    public function reindexEventsAction()
    {

    }

    /**
     * Rebiuld all processes index
     */
    public function reindexAllAction()
    {

    }

    /**
     * Mass rebuild selected processes index
     *
     */
    public function massReindexAction()
    {
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->messageManager->addError(__('Please select Indexes'));
        } else {
            try {
                $counter = 0;
                foreach ($processIds as $processId) {
                    /* @var $process \Magento\Index\Model\Process */
                    $process = $this->_indexer->getProcessById($processId);
                    if ($process && $process->getIndexer()->isVisible()) {
                        $process->reindexEverything();
                        $counter++;
                    }
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 index(es) have reindexed data.', $counter)
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('adminhtml/*/list');
    }

    /**
     * Mass change index mode of selected processes index
     *
     */
    public function massChangeModeAction()
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
                $this->messageManager->addSuccess(
                    __('Total of %1 index(es) have changed index mode.', $counter)
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('adminhtml/*/list');
    }

    /**
     * Check ACL permissins
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Index::index');
    }
}
