<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation reports controller
 *
 * @category   Magento
 * @package    Magento_Invitation
 */

class Magento_Invitation_Controller_Adminhtml_Report_Invitation extends Magento_Adminhtml_Controller_Action
{
    /**
     * Invitation Config
     *
     * @var Magento_Invitation_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Invitation_Model_Config $config
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Invitation_Model_Config $config
    ) {
        parent::__construct($context);
        $this->_config = $config;
    }

    /**
     * Init action breadcrumbs
     *
     * @return Magento_Invitation_Controller_Adminhtml_Report_Invitation
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(
                __('Reports'),
                __('Reports')
            )
            ->_addBreadcrumb(
                __('Invitations'),
                __('Invitations')
            );
        return $this;
    }

    /**
     * General report action
     */
    public function indexAction()
    {
        $this->_title(__('Invitations Report'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Invitation::report_magento_invitation_general')
            ->_addBreadcrumb(__('General Report'),
            __('General Report'))
            ->renderLayout();
    }

    /**
     * Export invitation general report grid to CSV format
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName   = 'invitation_general.csv';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export invitation general report grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_general.xml';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Report by customers action
     */
    public function customerAction()
    {
        $this->_title(__('Invited Customers Report'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Invitation::report_magento_invitation_customer')
            ->_addBreadcrumb(__('Invitation Report by Customers'),
            __('Invitation Report by Customers'))
            ->renderLayout();
    }

    /**
     * Export invitation customer report grid to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_customer.csv';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export invitation customer report grid to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $this->loadLayout();
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $fileName = 'invitation_customer.xml';
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Report by order action
     */
    public function orderAction()
    {
        $this->_title(__('Conversion Rate Report'));

        $this->_initAction()->_setActiveMenu('Magento_Invitation::report_magento_invitation_order')
            ->_addBreadcrumb(__('Invitation Report by Customers'),
            __('Invitation Report by Order Conversion Rate'))
            ->renderLayout();
    }

    /**
     * Export invitation order report grid to CSV format
     */
    public function exportOrderCsvAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_order.csv';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     * Export invitation order report grid to Excel XML format
     */
    public function exportOrderExcelAction()
    {
        $this->loadLayout();
        $fileName = 'invitation_order.xml';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Acl admin user check
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_config->isEnabled() &&
            $this->_authorization->isAllowed('Magento_Invitation::report_magento_invitation');
    }
}
