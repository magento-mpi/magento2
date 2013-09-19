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

class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Default limit collection
     *
     * @var int
     */
    protected $_defaultLimit = 0;

    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

    /**
     * Rma eav
     *
     * @var Magento_Rma_Helper_Eav
     */
    protected $_rmaEav = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Rma_Model_Item_Status
     */
    protected $_rmaItemStatus;

    /**
     * @param Magento_Rma_Model_Item_Status $rmaItemStatus
     * @param Magento_Rma_Helper_Eav $rmaEav
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Model_Item_Status $rmaItemStatus,
        Magento_Rma_Helper_Eav $rmaEav,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_rmaItemStatus = $rmaItemStatus;
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
        $this->setId('magento_rma_item_edit_grid');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setSortable(false);
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
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
     */
    protected function _prepareCollection()
    {
        $rma = $this->_coreRegistry->registry('current_rma');

        /** @var $collection Magento_Rma_Model_Resource_Item_Collection */
        $collection = $rma->getItemsForDisplay();

        if ($this->getItemFilter()) {
            $collection->addFilter('entity_id', $this->getItemFilter());
        }

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
        $rma = $this->_coreRegistry->registry('current_rma');
        if ($rma
            && (($rma->getStatus() === Magento_Rma_Model_Rma_Source_Status::STATE_CLOSED)
                || ($rma->getStatus() === Magento_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED))
        ) {
            $this->_rmaItemStatus->setOrderIsClosed();
        }

        $this->addColumn('product_admin_name', array(
            'header' => __('Product'),
            'type'   => 'text',
            'index'  => 'product_admin_name',
            'escape' => true,
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $this->addColumn('product_admin_sku', array(
            'header'=> __('SKU'),
            'type'  => 'text',
            'index' => 'product_admin_sku',
            'header_css_class'  => 'col-sku',
            'column_css_class'  => 'col-sku'
        ));

        //Renderer puts available quantity instead of order_item_id
        $this->addColumn('qty_ordered', array(
            'header'=> __('Remaining'),
            'getter'   => array($this, 'getQtyOrdered'),
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Quantity',
            'index' => 'qty_ordered',
            'order_data' => $this->getOrderItemsData(),
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('qty_requested', array(
            'header'=> __('Requested'),
            'index' => 'qty_requested',
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('qty_authorized', array(
            'header'=> __('Authorized'),
            'index' => 'qty_authorized',
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('qty_returned', array(
            'header'=> __('Returned'),
            'index' => 'qty_returned',
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('qty_approved', array(
            'header'=> __('Approved'),
            'index' => 'qty_approved',
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('reason', array(
            'header'=> __('Return Reason'),
            'getter'   => array($this, 'getReasonOptionStringValue'),
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Reasonselect',
            'options' => $this->_rmaEav->getAttributeOptionValues('reason'),
            'index' => 'reason',
            'header_css_class'  => 'col-reason',
            'column_css_class'  => 'col-reason'
        ));

        $this->addColumn('condition', array(
            'header'=> __('Item Condition'),
            'getter'   => array($this, 'getConditionOptionStringValue'),
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textselect',
            'options' => $this->_rmaEav->getAttributeOptionValues('condition'),
            'index' => 'condition',
            'header_css_class'  => 'col-condition',
            'column_css_class'  => 'col-condition'
        ));

        $this->addColumn('resolution', array(
            'header'=> __('Resolution'),
            'index' => 'resolution',
            'getter'   => array($this, 'getResolutionOptionStringValue'),
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textselect',
            'options' => $this->_rmaEav->getAttributeOptionValues('resolution'),
            'header_css_class'  => 'col-resolution',
            'column_css_class'  => 'col-resolution'
        ));

        $this->addColumn('status', array(
            'header'=> __('Status'),
            'index' => 'status',
            'getter'=> array($this, 'getStatusOptionStringValue'),
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Status',
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        $actionsArray = array(
            array(
                'caption'   => __('Details'),
                'class'     => 'item_details'
            ),
        );
        if (!($rma
            && (($rma->getStatus() === Magento_Rma_Model_Rma_Source_Status::STATE_CLOSED)
                || ($rma->getStatus() === Magento_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED))
        )) {
                $actionsArray[] = array(
                'caption'   => __('Split'),
                'class'     => 'item_split_line',
                'status_depended' => '1'
            );
        }

        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Action',
                'actions'   => $actionsArray,
                'is_system' => true,
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get available for return item quantity
     *
     * @param Magento_Object $row
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
     * @param Magento_Object $row
     * @return string
     */
    public function getReasonOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getReason());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Magento_Object $row
     * @return string
     */
    public function getResolutionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getResolution());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Magento_Object $row
     * @return string
     */
    public function getConditionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getCondition());
    }

    /**
     * Get string value of "Status" Attribute
     *
     * @param Magento_Object $row
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
     * Sets all available fields in editable state
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
     */
    public function setAllFieldsEditable()
    {
        $this->_rmaItemStatus->setAllEditable();
        return $this;
    }
}
