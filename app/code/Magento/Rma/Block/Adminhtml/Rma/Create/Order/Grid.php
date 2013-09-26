<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create order grid block
 */
class Magento_Rma_Block_Adminhtml_Rma_Create_Order_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * @var Magento_Sales_Model_Resource_Order_Grid_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Sales_Model_Order_Config
     */
    protected $_orderConfig;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Sales_Model_Resource_Order_Grid_CollectionFactory $collectionFactory
     * @param Magento_Sales_Model_Order_Config $orderConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Sales_Model_Resource_Order_Grid_CollectionFactory $collectionFactory,
        Magento_Sales_Model_Order_Config $orderConfig,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_orderConfig = $orderConfig;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Block constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('magento_rma_rma_create_order_grid');
        $this->setDefaultSort('entity_id');
    }

    /**
     * Prepare grid collection object
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Create_Order_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Magento_Sales_Model_Resource_Order_Grid_Collection */
        $collection = $this->_collectionFactory->create();
        $collection->setOrder('entity_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header' => __('Order'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => __('Purchase Point'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => __('Purchase Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => __('Bill-to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => __('Ship-to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => __('Grand Total (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => __('Grand Total (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => $this->_orderConfig->getStatuses(),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/new', array('order_id' => $row->getId()));
    }

}
