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
 * Adminhtml customer orders grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_Orders extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Sales reorder
     *
     * @var Magento_Sales_Helper_Reorder
     */
    protected $_salesReorder = null;
    
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Sales_Model_Resource_Order_Grid_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Sales_Model_Resource_Order_Grid_CollectionFactory $collectionFactory
     * @param Magento_Sales_Helper_Reorder $salesReorder
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Model_Resource_Order_Grid_CollectionFactory $collectionFactory,
        Magento_Sales_Helper_Reorder $salesReorder,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_salesReorder = $salesReorder;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create()
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('store_id')
            ->addFieldToSelect('billing_name')
            ->addFieldToSelect('shipping_name')
            ->addFieldToFilter('customer_id', $this->_coreRegistry->registry('current_customer')->getId())
            ->setIsCustomerMode(true);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => __('Order'),
            'width'     => '100',
            'index'     => 'increment_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => __('Purchase Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('billing_name', array(
            'header'    => __('Bill-to Name'),
            'index'     => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header'    => __('Ship-to Name'),
            'index'     => 'shipping_name',
        ));

        $this->addColumn('grand_total', array(
            'header'    => __('Order Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => __('Purchase Point'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view' => true
            ));
        }

        if ($this->_salesReorder->isAllow()) {
            $this->addColumn('action', array(
                'header'    => ' ',
                'filter'    => false,
                'sortable'  => false,
                'width'     => '100px',
                'renderer'  => 'Magento_Adminhtml_Block_Sales_Reorder_Renderer_Action'
            ));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/orders', array('_current' => true));
    }
}
