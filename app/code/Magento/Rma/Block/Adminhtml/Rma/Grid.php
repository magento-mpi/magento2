<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Rma;

/**
 * RMA Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Rma grid collection
     *
     * @var \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Rma model
     *
     * @var \Magento\Rma\Model\RmaFactory
     */
    protected $_rmaFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory,
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_rmaFactory = $rmaFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('rmaGrid');
        $this->setDefaultSort('date_requested');
        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare related item collection
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Grid
     */
    protected function _prepareCollection()
    {
        $this->_beforePrepareCollection();
        return parent::_prepareCollection();
    }

    /**
     * Configuring and setting collection
     *
     * @return $this
     */
    protected function _beforePrepareCollection()
    {
        if (!$this->getCollection()) {
            /** @var $collection \Magento\Rma\Model\Resource\Rma\Grid\Collection */
            $collection = $this->_collectionFactory->create();
            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            array(
                'header' => __('RMA'),
                'type' => 'number',
                'index' => 'increment_id',
                'header_css_class' => 'col-rma-number',
                'column_css_class' => 'col-rma-number'
            )
        );

        $this->addColumn(
            'date_requested',
            array(
                'header' => __('Requested Date'),
                'index' => 'date_requested',
                'type' => 'datetime',
                'html_decorators' => array('nobr'),
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            )
        );

        $this->addColumn(
            'order_increment_id',
            array(
                'header' => __('Order'),
                'type' => 'number',
                'index' => 'order_increment_id',
                'header_css_class' => 'col-order-number',
                'column_css_class' => 'col-order-number'
            )
        );

        $this->addColumn(
            'order_date',
            array(
                'header' => __('Order Date'),
                'index' => 'order_date',
                'type' => 'datetime',
                'html_decorators' => array('nobr'),
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            )
        );

        $this->addColumn(
            'customer_name',
            array(
                'header' => __('Customer'),
                'index' => 'customer_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            )
        );
        /** @var $rmaModel \Magento\Rma\Model\Rma */
        $rmaModel = $this->_rmaFactory->create();
        $this->addColumn(
            'status',
            array(
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $rmaModel->getAllStatuses(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
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
                        'url' => array('base' => $this->_getControllerUrl('edit')),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_ids');

        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => __('Close'),
                'url' => $this->getUrl($this->_getControllerUrl('close')),
                'confirm' => __(
                    "You have chosen to change status(es) of the selected RMA requests to Close. Are you sure you want to proceed?"
                )
            )
        );

        return $this;
    }

    /**
     * Get Url to action
     *
     * @param  string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return '*/*/' . $action;
    }

    /**
     * Retrieve row url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl($this->_getControllerUrl('edit'), array('id' => $row->getId()));
    }
}
