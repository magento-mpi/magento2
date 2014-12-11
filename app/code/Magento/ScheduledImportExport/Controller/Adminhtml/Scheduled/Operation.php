<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Operation controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled;

class Operation extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initialize layout.
     *
     * @return $this
     */
    protected function _initAction()
    {
        try {
            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_ScheduledImportExport::system_convert_magento_scheduled_operation');
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Scheduled Imports/Exports'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/scheduled_operation/index');
        }

        return $this;
    }

    /**
     * Check access (in the ACL) for current user.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_ScheduledImportExport::magento_scheduled_operation');
    }
}
