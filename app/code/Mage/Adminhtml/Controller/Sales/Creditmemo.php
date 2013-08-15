<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Sales_Creditmemo extends Mage_Adminhtml_Controller_Sales_Creditmemo_CreditmemoAbstract
{
    /**
     * Export credit memo grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'creditmemos.csv';
        $grid       = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Sales_Creditmemo_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export credit memo grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'creditmemos.xml';
        $grid       = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Sales_Creditmemo_Grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     *  Index page
     */
    public function indexAction()
    {
        $this->_title($this->__('Credit Memos'));
        parent::indexAction();
    }
}
