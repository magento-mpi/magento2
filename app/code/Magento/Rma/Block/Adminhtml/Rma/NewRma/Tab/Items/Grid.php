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
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items;

class Grid
    extends \Magento\Backend\Block\Widget\Grid\Extended
//    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid
{
    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

    /**
     * Rma eav
     *
     * @var \Magento\Rma\Helper\Eav
     */
    protected $_rmaEav = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Rma\Helper\Eav $rmaEav
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Eav $rmaEav,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_rmaEav = $rmaEav;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Block constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_items_grid');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->_gatherOrderItemsData();
    }

    /**
     * Gather items quantity data from Order item collection
     *
     * @return void
     */
    protected function _gatherOrderItemsData()
    {
        $itemsData = array();
        if ($this->_coreRegistry->registry('current_order')) {
            foreach ($this->_coreRegistry->registry('current_order')->getItemsCollection() as $item) {
                $itemsData[$item->getId()] = array(
                    'qty_shipped' => $item->getQtyShipped(),
                    'qty_returned' => $item->getQtyReturned()
                );
            }
        }
        $this->setOrderItemsData($itemsData);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Rma\Model\Resource\Item\Collection */
        $collection = \Mage::getResourceModel('Magento\Rma\Model\Resource\Item\Collection');
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('entity_id', NULL);

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
        $this->addColumn('product_name', array(
            'header'   => __('Product'),
            'type'     => 'text',
            'index'    => 'product_name',
            'sortable' => false,
            'escape'   => true,
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $this->addColumn('product_sku', array(
            'header'   => __('SKU'),
            'type'     => 'text',
            'index'    => 'product_sku',
            'sortable' => false,
            'escape'   => true,
            'header_css_class'  => 'col-sku',
            'column_css_class'  => 'col-sku'
        ));

        //Renderer puts available quantity instead of order_item_id
        $this->addColumn('qty_ordered', array(
            'header'=> __('Remaining'),
            'getter'   => array($this, 'getQtyOrdered'),
            'type'  => 'text',
            'index' => 'qty_ordered',
            'sortable' => false,
            'order_data' => $this->getOrderItemsData(),
            'renderer'  => 'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer\Quantity',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('qty_requested', array(
            'header'=> __('Requested'),
            'index' => 'qty_requested',
            'type'  => 'input',
            'sortable' => false,
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $eavHelper = $this->_rmaEav;
        $this->addColumn('reason', array(
            'header'=> __('Return Reason'),
            'getter'   => array($this, 'getReasonOptionStringValue'),
            'type'  => 'select',
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('reason'),
            'index' => 'reason',
            'sortable' => false,
            'header_css_class'  => 'col-reason',
            'column_css_class'  => 'col-reason'
        ));

        $this->addColumn('condition', array(
            'header'=> __('Item Condition'),
            'type'  => 'select',
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('condition'),
            'index' => 'condition',
            'sortable' => false,
            'header_css_class'  => 'col-condition',
            'column_css_class'  => 'col-condition'
        ));

        $this->addColumn('resolution', array(
            'header'=> __('Resolution'),
            'index' => 'resolution',
            'type'  => 'select',
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('resolution'),
            'sortable' => false,
            'header_css_class'  => 'col-resolution',
            'column_css_class'  => 'col-resolution'
        ));

        $actionsArray = array(
            array(
                'caption'   => __('Delete'),
                'url'       => array('base'=> '*/*/delete'),
                'field'     => 'id',
                'onclick'  => 'alert(\'Delete\');return false;'
            ),
            array(
                'caption'   => __('Add Details'),
                'url'       => array('base'=> '*/*/edit'),
                'field'     => 'id',
                'onclick'  => 'alert(\'Details\');return false;'
            ),
        );

        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'renderer'  => 'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer\Action',
                'actions'   => $actionsArray,
                'sortable'  => false,
                'is_system' => true,
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get available for return item quantity
     *
     * @param \Magento\Object $row
     * @return int
     */
    public function getQtyOrdered($row)
    {
        $orderItemsData = $this->getOrderItemsData();
        if (is_array($orderItemsData)
                && isset($orderItemsData[$row->getOrderItemId()])
                && isset($orderItemsData[$row->getOrderItemId()]['qty_shipped'])
                && isset($orderItemsData[$row->getOrderItemId()]['qty_returned'])) {
            $return = $orderItemsData[$row->getOrderItemId()]['qty_shipped'] -
                    $orderItemsData[$row->getOrderItemId()]['qty_returned'];
        } else {
            $return = 0;
        }
        return $return;
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getReasonOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getReason());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getResolutionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getResolution());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getConditionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getCondition());
    }

    /**
     * Get string value of "Status" Attribute
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getStatusOptionStringValue($row)
    {
        return $row->getStatusLabel();
    }

    /**
     * Get string value option-type attribute by it's unique int value
     *
     * @param int $value
     * @return string
     */
    protected function _getAttributeOptionStringValue($value)
    {
        if (is_null($this->_attributeOptionValues)) {
            $this->_attributeOptionValues = $this->_rmaEav->getAttributeOptionStringValues();
        }
        if (isset($this->_attributeOptionValues[$value])) {
            return $this->escapeHtml($this->_attributeOptionValues[$value]);
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Object
     * @return string
     */
    public function getRowUrl($item)
    {
        //$res = parent::getRowUrl($item);
        return null;
    }
}
