<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Log and archive grids controller
 */
namespace Magento\Logging\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Logging extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Event model factory
     *
     * @var \Magento\Logging\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * Archive model factory
     *
     * @var \Magento\Logging\Model\ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Logging\Model\EventFactory $eventFactory
     * @param \Magento\Logging\Model\ArchiveFactory $archiveFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Logging\Model\EventFactory $eventFactory,
        \Magento\Logging\Model\ArchiveFactory $archiveFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_eventFactory = $eventFactory;
        $this->_archiveFactory = $archiveFactory;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * permissions checker
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'archive':
            case 'download':
            case 'archiveGrid':
                return $this->_authorization->isAllowed('Magento_Logging::backups');
                break;
            case 'grid':
            case 'exportCsv':
            case 'exportXml':
            case 'details':
            case 'index':
                return $this->_authorization->isAllowed('Magento_Logging::magento_logging_events');
                break;
        }
    }
}
