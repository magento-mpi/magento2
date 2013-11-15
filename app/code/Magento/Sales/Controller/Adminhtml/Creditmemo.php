<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Controller\Adminhtml;

class Creditmemo extends \Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo
{
    /**
     * Export credit memo grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'creditmemos.csv';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Creditmemo\Grid');
        return $this->_fileFactory->create($fileName, $grid->getCsvFile());
    }

    /**
     *  Export credit memo grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'creditmemos.xml';
        $grid       = $this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Creditmemo\Grid');
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName));
    }

    /**
     *  Index page
     */
    public function indexAction()
    {
        $this->_title->add(__('Credit Memos'));
        parent::indexAction();
    }
}
