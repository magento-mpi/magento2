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
        $fileName = 'creditmemos.csv';
        /** @var  $grid  \Magento\Sales\Block\Adminhtml\Creditmemo\Grid*/
        $grid = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Creditmemo\Grid');
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
        $fileName = 'creditmemos.xml';
        $grid = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Creditmemo\Grid');
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
