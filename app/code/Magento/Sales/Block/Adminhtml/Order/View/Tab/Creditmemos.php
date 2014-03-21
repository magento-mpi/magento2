<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\View\Tab;

/**
 * Order Credit Memos grid
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Creditmemos extends \Magento\Backend\Block\Widget\Grid\Extended implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    protected $_orderCreditmemo;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Collection\Factory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Resource\Order\Collection\Factory $collectionFactory
     * @param \Magento\Sales\Model\Order\Creditmemo $orderCreditmemo
     * @param \Magento\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Resource\Order\Collection\Factory $collectionFactory,
        \Magento\Sales\Model\Order\Creditmemo $orderCreditmemo,
        \Magento\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_orderCreditmemo = $orderCreditmemo;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('order_creditmemos');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\Sales\Model\Resource\Order\Creditmemo\Grid\Collection';
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create(
            $this->_getCollectionClass()
        )->addFieldToSelect(
            'entity_id'
        )->addFieldToSelect(
            'created_at'
        )->addFieldToSelect(
            'increment_id'
        )->addFieldToSelect(
            'order_currency_code'
        )->addFieldToSelect(
            'store_currency_code'
        )->addFieldToSelect(
            'base_currency_code'
        )->addFieldToSelect(
            'state'
        )->addFieldToSelect(
            'grand_total'
        )->addFieldToSelect(
            'base_grand_total'
        )->addFieldToSelect(
            'billing_name'
        )->setOrderFilter(
            $this->getOrder()
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Initialize grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            array(
                'header' => __('Credit Memo'),
                'index' => 'increment_id',
                'header_css_class' => 'col-memo',
                'column_css_class' => 'col-memo'
            )
        );

        $this->addColumn(
            'billing_name',
            array(
                'header' => __('Bill-to Name'),
                'index' => 'billing_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => __('Created'),
                'index' => 'created_at',
                'type' => 'datetime',
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            )
        );

        $this->addColumn(
            'state',
            array(
                'header' => __('Status'),
                'index' => 'state',
                'type' => 'options',
                'options' => $this->_orderCreditmemo->getStates(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            )
        );

        $this->addColumn(
            'base_grand_total',
            array(
                'header' => __('Refunded'),
                'index' => 'base_grand_total',
                'type' => 'currency',
                'currency' => 'base_currency_code',
                'header_css_class' => 'col-refunded',
                'column_css_class' => 'col-refunded'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Row URL getter
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/order_creditmemo/view',
            array('creditmemo_id' => $row->getId(), 'order_id' => $row->getOrderId())
        );
    }

    /**
     * Grid URL getter
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/*/creditmemos', array('_current' => true));
    }

    /**
     * ######################## TAB settings #################################
     */

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Credit Memos');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order Credit Memos');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
