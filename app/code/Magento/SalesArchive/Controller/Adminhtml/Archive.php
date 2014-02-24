<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Controller\Adminhtml;
use Magento\App\ResponseInterface;

/**
 * Archive controller
 */
class Archive extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\SalesArchive\Model\Archive
     */
    protected $_archiveModel;

    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\SalesArchive\Model\Archive $archiveModel
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\SalesArchive\Model\Archive $archiveModel,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_archiveModel = $archiveModel;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Render archive grid
     *
     * @return $this
     */
    protected function _renderGrid()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
        return $this;
    }

    /**
     * Orders view page
     *
     * @return void
     */
    public function ordersAction()
    {
        $this->_title->add(__('Orders'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_orders');
        $this->_view->renderLayout();
    }

    /**
     * Orders grid
     *
     * @return void
     */
    public function ordersGridAction()
    {
        $this->_renderGrid();
    }

    /**
     * Invoices view page
     *
     * @return void
     */
    public function invoicesAction()
    {
        $this->_title->add(__('Invoices'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_invoices');
        $this->_view->renderLayout();
    }

    /**
     * Invoices grid
     *
     * @return void
     */
    public function invoicesGridAction()
    {
        $this->_renderGrid();
    }


    /**
     * Creditmemos view page
     *
     * @return void
     */
    public function creditmemosAction()
    {
        $this->_title->add(__('Credit Memos'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_creditmemos');
        $this->_view->renderLayout();
    }

    /**
     * Creditmemos grid
     *
     * @return void
     */
    public function creditmemosGridAction()
    {
        $this->_renderGrid();
    }

    /**
     * Shipments view page
     *
     * @return void
     */
    public function shipmentsAction()
    {
        $this->_title->add(__('Shipments'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_SalesArchive::sales_archive_shipments');
        $this->_view->renderLayout();
    }

    /**
     * Shipments grid
     *
     * @return void
     */
    public function shipmentsGridAction()
    {
        $this->_renderGrid();
    }


    /**
     * Cancel orders mass action
     *
     * @return void
     */
    public function massCancelAction()
    {
        $this->_forward('massCancel', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Hold orders mass action
     *
     * @return void
     */
    public function massHoldAction()
    {
        $this->_forward('massHold', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Unhold orders mass action
     *
     * @return void
     */
    public function massUnholdAction()
    {
        $this->_forward('massUnhold', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Massaction for removing orders from archive
     *
     * @return void
     */
    public function massRemoveAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $removedFromArchive = $this->_archiveModel->removeOrdersFromArchiveById($orderIds);

        $removedFromArchiveCount = count($removedFromArchive);
        if ($removedFromArchiveCount>0) {
            $this->messageManager->addSuccess(__('We removed %1 order(s) from the archive.', $removedFromArchiveCount));
        } else {
            // selected orders is not available for removing from archive
        }
        $this->_redirect('adminhtml/*/orders');
    }

    /**
     * Massaction for adding orders to archive
     *
     * @return void
     */
    public function massAddAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $archivedIds = $this->_archiveModel->archiveOrdersById($orderIds);

        $archivedCount = count($archivedIds);
        if ($archivedCount>0) {
            $this->messageManager->addSuccess(__('We archived %1 order(s).', $archivedCount));
        } else {
            $this->messageManager->addWarning(__("We can't archive the selected order(s)."));
        }
        $this->_redirect('sales/order/');
    }

    /**
     * Archive order action
     *
     * @return void
     */
    public function addAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $this->_archiveModel->archiveOrdersById($orderId);
            $this->messageManager->addSuccess(__('We have archived the order.'));
            $this->_redirect('sales/order/view', array('order_id'=>$orderId));
        } else {
            $this->messageManager->addError(__('Please specify the order ID to be archived.'));
            $this->_redirect('sales/order');
        }
    }

    /**
     * Unarchive order action
     *
     * @return void
     */
    public function removeAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $this->_archiveModel->removeOrdersFromArchiveById($orderId);
            $this->messageManager->addSuccess(__('We have removed the order from the archive.'));
            $this->_redirect('sales/order/view', array('order_id'=>$orderId));
        } else {
            $this->messageManager->addError(__('Please specify the order ID to be removed from archive.'));
            $this->_redirect('sales/order');
        }
    }

    /**
     * Print invoices mass action
     *
     * @return void
     */
    public function massPrintInvoicesAction()
    {
        $this->_forward('pdfinvoices', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print Credit Memos mass action
     *
     * @return void
     */
    public function massPrintCreditMemosAction()
    {
        $this->_forward('pdfcreditmemos', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print all documents mass action
     *
     * @return void
     */
    public function massPrintAllDocumentsAction()
    {
        $this->_forward('pdfdocs', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print packing slips mass action
     *
     * @return void
     */
    public function massPrintPackingSlipsAction()
    {
        $this->_forward('pdfshipments', 'sales_order', null, array('origin' => 'archive'));
    }

    /**
     * Print shipping labels mass action
     *
     * @return void
     */
    public function massPrintShippingLabelAction()
    {
        $this->_forward('massPrintShippingLabel', 'sales_order_shipment', null, array('origin' => 'archive'));
    }

    /**
     * Export order grid to CSV format
     *
     * @return void
     */
    public function exportCsvAction()
    {
        $this->_export('csv');
    }

    /**
     *  Export order grid to Excel XML format
     *
     * @return void
     */
    public function exportExcelAction()
    {
        $this->_export('xml');
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param string $type
     * @return ResponseInterface
     */
    protected function _export($type)
    {
        $action = strtolower((string)$this->getRequest()->getParam('action'));
        $this->_view->loadLayout(false);
        $layout = $this->_view->getLayout();

        switch ($action) {
            case 'invoice':
                $fileName = 'invoice_archive.' . $type;
                $grid = $layout->createBlock('Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Invoice\Grid');
                break;
            case 'shipment':
                $fileName = 'shipment_archive.' . $type;
                $grid = $layout->createBlock('Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Shipment\Grid');
                break;
            case 'creditmemo':
                $fileName = 'creditmemo_archive.' . $type;
                $grid = $layout->createBlock('Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Creditmemo\Grid');
                break;
            default:
                $fileName = 'orders_archive.' . $type;
                /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $grid  */
                $grid = $layout->getChildBlock('sales.order.grid', 'grid.export');
                break;
        }

        if ($type == 'csv') {
            return $this->_fileFactory->create($fileName, $grid->getCsvFile(), \Magento\App\Filesystem::VAR_DIR);
        } else {
            return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), \Magento\App\Filesystem::VAR_DIR);
        }
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch (strtolower($this->getRequest()->getActionName())) {
            case 'orders':
            case 'ordersgrid':
                $acl = 'Magento_SalesArchive::orders';
                break;

            case 'invoices':
            case 'invoicesgrid':
                $acl = 'Magento_SalesArchive::invoices';
                break;

            case 'creditmemos':
            case 'creditmemosgrid':
                $acl = 'Magento_SalesArchive::creditmemos';
                break;

            case 'shipments':
            case 'shipmentsgrid':
                $acl = 'Magento_SalesArchive::shipments';
                break;

            case 'massadd':
            case 'add':
                $acl = 'Magento_SalesArchive::add';
                break;

            case 'massremove':
            case 'remove':
                $acl = 'Magento_SalesArchive::remove';
                break;

            default:
                $acl = 'Magento_SalesArchive::orders';
                break;
        }

        return $this->_authorization->isAllowed($acl);
    }
}
