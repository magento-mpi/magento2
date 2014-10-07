<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Store controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Store extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->filterManager = $filterManager;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Backend::system_store'
        )->_addBreadcrumb(
            __('System'),
            __('System')
        )->_addBreadcrumb(
            __('Manage Stores'),
            __('Manage Stores')
        );
        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::store');
    }

    /**
     * Backup database
     *
     * @param string $failPath redirect path if backup failed
     * @param array $arguments
     * @return $this|void
     */
    protected function _backupDatabase($failPath, $arguments = array())
    {
        if (!$this->getRequest()->getParam('create_backup')) {
            return $this;
        }
        try {
            $backupDb = $this->_objectManager->create('Magento\Backup\Model\Db');
            $backup = $this->_objectManager->create(
                'Magento\Backup\Model\Backup'
            )->setTime(
                time()
            )->setType(
                'db'
            )->setPath(
                $this->_objectManager->get(
                    'Magento\Framework\App\Filesystem'
                )->getPath(
                    DirectoryList::VAR_DIR
                ) . '/backups'
            );

            $backupDb->createBackup($backup);
            $this->messageManager->addSuccess(__('The database was backed up.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect($failPath, $arguments);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('We couldn\'t create a backup right now. Please try again later.')
            );
            $this->_redirect($failPath, $arguments);
            return;
        }
        return $this;
    }

    /**
     * Add notification on deleting store / store view / website
     *
     * @param string $typeTitle
     * @return $this
     */
    protected function _addDeletionNotice($typeTitle)
    {
        $this->messageManager->addNotice(
            __(
                'Deleting a %1 will not delete the information associated with the %1 (e.g. categories, products, etc.), but the %1 will not be able to be restored. It is suggested that you create a database backup before deleting the %1.',
                $typeTitle
            )
        );
        return $this;
    }
}
