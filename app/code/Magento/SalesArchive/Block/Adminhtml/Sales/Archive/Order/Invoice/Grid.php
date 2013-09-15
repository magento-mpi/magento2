<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive invoices grid block
 *
 */

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Invoice;

class Grid
    extends \Magento\Adminhtml\Block\Sales\Invoice\Grid
{
    /**
     * Core url
     *
     * @var Magento_Core_Helper_Url
     */
    protected $_coreUrl = null;

    /**
     * @param Magento_Core_Helper_Url $coreUrl
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Url $coreUrl,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_coreUrl = $coreUrl;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_invoice_grid_archive');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\SalesArchive\Model\Resource\Order\Invoice\Collection';
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('*/*/invoicesgrid', array('_current' => true));
    }

    /**
     * Retrieve grid export types
     *
     * @return array|false
     */
    public function getExportTypes()
    {
        if (!empty($this->_exportTypes)) {
            foreach ($this->_exportTypes as $exportType) {
                $url = $this->_coreUrl->removeRequestParam($exportType->getUrl(), 'action');
                $exportType->setUrl($this->_coreUrl->addRequestParam($url, array('action' => 'invoice')));
            }
            return $this->_exportTypes;
        }
        return false;
    }
}
