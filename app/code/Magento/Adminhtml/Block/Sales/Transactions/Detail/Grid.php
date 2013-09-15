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
 * Adminhtml transaction details grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Transactions\Detail;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize default sorting and html ID
     */
    protected function _construct()
    {
        $this->setId('transactionDetailsGrid');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare collection for grid
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = new \Magento\Data\Collection();
        foreach ($this->getTransactionAdditionalInfo() as $key => $value) {
            $data = new \Magento\Object(array('key' => $key, 'value' => $value));
            $collection->addItem($data);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('key', array(
            'header'    => __('Key'),
            'index'     => 'key',
            'sortable'  => false,
            'type'      => 'text',
            'header_css_class'  => 'col-key',
            'column_css_class'  => 'col-key'
        ));

        $this->addColumn('value', array(
            'header'    => __('Value'),
            'index'     => 'value',
            'sortable'  => false,
            'type'      => 'text',
            'escape'    => true,
            'header_css_class'  => 'col-value',
            'column_css_class'  => 'col-value'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Transaction addtitional info
     *
     * @return array
     */
    public function getTransactionAdditionalInfo()
    {
        $info = $this->_coreRegistry->registry('current_transaction')->getAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS
        );
        return (is_array($info)) ? $info : array();
    }
}
