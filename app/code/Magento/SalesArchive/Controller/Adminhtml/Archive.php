<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

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
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\SalesArchive\Model\Archive $archiveModel
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\SalesArchive\Model\Archive $archiveModel,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
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
     * Declare headers and content file in response for file download
     *
     * @param string $type
     * @return ResponseInterface
     */
    protected function _export($type)
    {
        $this->_view->loadLayout(false);
        $layout = $this->_view->getLayout();

        $fileName = 'orders_archive.' . $type;
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $grid */
        $grid = $layout->getChildBlock('sales.order.grid', 'grid.export');

        if ($type == 'csv') {
            return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
        } else {
            return $this->_fileFactory->create(
                $fileName,
                $grid->getExcelFile($fileName),
                DirectoryList::VAR_DIR
            );
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
