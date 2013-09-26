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
namespace Magento\Rma\Block\Adminhtml\Rma\Create\Order;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory $collectionFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
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
     * @return \Magento\Rma\Block\Adminhtml\Rma\Create\Order\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Sales\Model\Resource\Order\Grid\Collection */
        $collection = $this->_collectionFactory->create();
        $collection->setOrder('entity_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
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
