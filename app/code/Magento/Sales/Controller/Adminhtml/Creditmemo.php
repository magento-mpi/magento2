<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml;

use Magento\App\ResponseInterface;

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Creditmemo extends \Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo
{
    /**
     * Export credit memo grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCsvAction()
    {
        $this->_view->loadLayout(false);
        $fileName = 'creditmemos.csv';
        $grid = $grid = $this->_view->getLayout()->getChildBlock('sales.creditmemo.grid', 'grid.export');
        $csvFile = $grid->getCsvFile();
        return $this->_fileFactory->create($fileName, $csvFile, \Magento\App\Filesystem::VAR_DIR);
    }

    /**
     * Export credit memo grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function exportExcelAction()
    {
        $this->_view->loadLayout(false);
        $fileName = 'creditmemos.xml';
        $grid = $grid = $this->_view->getLayout()->getChildBlock('sales.creditmemo.grid', 'grid.export');
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), \Magento\App\Filesystem::VAR_DIR);
    }

    /**
     * Index page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Credit Memos'));
        parent::indexAction();
    }
}
