<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Report;

use Magento\Framework\App\ViewInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Invitation reports controller
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Invitation extends \Magento\Backend\App\Action
{
    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Invitation\Model\Config $config,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->_config = $config;
    }

    /**
     * Init action breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        $this->_addBreadcrumb(__('Invitations'), __('Invitations'));
        return $this;
    }

    /**
     * General report action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Invitations Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_general'
        )->_addBreadcrumb(
            __('General Report'),
            __('General Report')
        );
        $this->_view->renderLayout();
    }

    /**
     * Export invitation general report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCsvAction()
    {
        $this->_view->loadLayout();
        $fileName = 'invitation_general.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Export invitation general report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportExcelAction()
    {
        $this->_view->loadLayout();
        $fileName = 'invitation_general.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Report by customers action
     *
     * @return void
     */
    public function customerAction()
    {
        $this->_title->add(__('Invited Customers Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_customer'
        )->_addBreadcrumb(
            __('Invitation Report by Customers'),
            __('Invitation Report by Customers')
        );
        $this->_view->renderLayout();
    }

    /**
     * Export invitation customer report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCustomerCsvAction()
    {
        $this->_view->loadLayout();
        $fileName = 'invitation_customer.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Export invitation customer report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportCustomerExcelAction()
    {
        $this->_view->loadLayout();
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $fileName = 'invitation_customer.xml';
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Report by order action
     *
     * @return void
     */
    public function orderAction()
    {
        $this->_title->add(__('Conversion Rate Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_order'
        )->_addBreadcrumb(
            __('Invitation Report by Customers'),
            __('Invitation Report by Order Conversion Rate')
        );
        $this->_view->renderLayout();
    }

    /**
     * Export invitation order report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportOrderCsvAction()
    {
        $this->_view->loadLayout();
        $fileName = 'invitation_order.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Export invitation order report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportOrderExcelAction()
    {
        $this->_view->loadLayout();
        $fileName = 'invitation_order.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            \Magento\Framework\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Acl admin user check
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_config->isEnabled() && $this->_authorization->isAllowed(
            'Magento_Invitation::report_magento_invitation'
        );
    }
}
