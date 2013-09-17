<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Sales_Creditmemo extends Magento_Adminhtml_Controller_Sales_Creditmemo_CreditmemoAbstract
{
    /**
     * Export credit memo grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'creditmemos.csv';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Creditmemo_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export credit memo grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'creditmemos.xml';
        $grid       = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Creditmemo_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     *  Index page
     */
    public function indexAction()
    {
        $this->_title(__('Credit Memos'));
        parent::indexAction();
    }
}
