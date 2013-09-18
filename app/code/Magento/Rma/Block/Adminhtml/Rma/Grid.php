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
 * RMA Grid
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * @var Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory
     */
    protected $_gridCollFactory;

    /**
     * @param Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $gridCollFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $gridCollFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_gridCollFactory = $gridCollFactory;
        parent::__construct(
            $coreData, $context, $storeManager, $urlModel, $data
        );
    }

    /**
     * Initialize grid
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
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareCollection()
    {
        $this->_beforePrepareCollection();
        return parent::_prepareCollection();
    }

    /**
     * Configuring and setting collection
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _beforePrepareCollection()
    {
        if (!$this->getCollection()) {
            $collection = $this->_gridCollFactory->create();
            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header' => __('RMA'),
            'type'   => 'number',
            'index'  => 'increment_id',
            'header_css_class'  => 'col-rma-number',
            'column_css_class'  => 'col-rma-number'
        ));

        $this->addColumn('date_requested', array(
            'header' => __('Requested Date'),
            'index' => 'date_requested',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('order_increment_id', array(
            'header' => __('Order'),
            'type'   => 'number',
            'index'  => 'order_increment_id',
            'header_css_class'  => 'col-order-number',
            'column_css_class'  => 'col-order-number'
        ));

        $this->addColumn('order_date', array(
            'header' => __('Order Date'),
            'index' => 'order_date',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('customer_name', array(
            'header' => __('Customer'),
            'index' => 'customer_name',
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('status', array(
            'header'  => __('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'options' => Mage::getModel('Magento_Rma_Model_Rma')->getAllStatuses(),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => __('View'),
                        'url'       => array('base'=> $this->_getControllerUrl('edit')),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_ids');

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> __('Close'),
            'url'  => $this->getUrl($this->_getControllerUrl('close')),
            'confirm'  => __("You have chosen to change status(es) of the selected RMA requests to Close. Are you sure you want to proceed?")
        ));

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
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl($this->_getControllerUrl('edit'), array(
            'id' => $row->getId()
        ));
    }
}
