<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\Framework\Registry
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
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Index\Model\ProcessFactory $processFactory
     * @param \Magento\Index\Model\Indexer $indexer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
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
     * Check ACL permissins
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Index::index');
    }
}
