<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Create\Order;

/**
 * Admin RMA create order grid block
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Sales order config model
     *
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * Sales order collection
     *
     * @var \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory
     */
    protected $_gridCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory $gridCollectionFactory
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory $gridCollectionFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        $this->_gridCollectionFactory = $gridCollectionFactory;
        $this->_orderConfig = $orderConfig;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Block constructor
     *
     * @return void
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
        $collection = $this->_gridCollectionFactory->create()->setOrder('entity_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'real_order_id',
            ['header' => __('Order'), 'width' => '80px', 'type' => 'text', 'index' => 'increment_id']
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                [
                    'header' => __('Purchase Point'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_view' => true,
                    'display_deleted' => true
                ]
            );
        }

        $this->addColumn(
            'created_at',
            ['header' => __('Purchase Date'), 'index' => 'created_at', 'type' => 'datetime']
        );

        $this->addColumn(
            'billing_name',
            [
                'header' => __('Bill-to Name'),
                'index' => 'billing_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'

            ]
        );

        $this->addColumn(
            'shipping_name',
            [
                'header' => __('Ship-to Name'),
                'index' => 'shipping_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'base_grand_total',
            [
                'header' => __('Grand Total (Base)'),
                'index' => 'base_grand_total',
                'type' => 'currency',
                'currency' => 'base_currency_code'
            ]
        );

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Grand Total (Purchased)'),
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'width' => '70px',
                'options' => $this->_orderConfig->getStatuses()
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/new', ['order_id' => $row->getId()]);
    }
}
