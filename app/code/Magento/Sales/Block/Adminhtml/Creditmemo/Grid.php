<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Creditmemo;

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Creditmemo\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoFactory
     */
    protected $_creditmemoFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory
     * @param \Magento\Sales\Model\Resource\Order\Creditmemo\Grid\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Resource\Order\Collection\Factory $collectionFactory,
        array $data = array()
    ) {
        $this->_creditmemoFactory = $creditmemoFactory;
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
        $this->setId('sales_creditmemo_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return '\Magento\Sales\Model\Resource\Order\Creditmemo\Grid\Collection';
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
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
                'type' => 'text',
                'header_css_class' => 'col-memo-number',
                'column_css_class' => 'col-memo-number'
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
            'order_increment_id',
            array(
                'header' => __('Order'),
                'index' => 'order_increment_id',
                'type' => 'text',
                'header_css_class' => 'col-order-number',
                'column_css_class' => 'col-order-number'
            )
        );

        $this->addColumn(
            'order_created_at',
            array(
                'header' => __('Order Date'),
                'index' => 'order_created_at',
                'type' => 'datetime',
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
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
            'state',
            array(
                'header' => __('Status'),
                'index' => 'state',
                'type' => 'options',
                'options' => $this->_creditmemoFactory->create()->getStates(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            )
        );

        $this->addColumn(
            'grand_total',
            array(
                'header' => __('Refunded'),
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code',
                'header_css_class' => 'col-refunded',
                'column_css_class' => 'col-refunded'
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => __('View'),
                        'url' => array('base' => 'sales/creditmemo/view'),
                        'field' => 'creditmemo_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            )
        );

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('creditmemo_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem(
            'pdfcreditmemos_order',
            array('label' => __('PDF Credit Memos'), 'url' => $this->getUrl('sales/creditmemo/pdfcreditmemos'))
        );

        return $this;
    }

    /**
     * Get row url
     *
     * @param \Magento\Object $row
     * @return false|string
     */
    public function getRowUrl($row)
    {
        if (!$this->_authorization->isAllowed(null)) {
            return false;
        }

        return $this->getUrl('sales/creditmemo/view', array('creditmemo_id' => $row->getId()));
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/*/*', array('_current' => true));
    }
}
