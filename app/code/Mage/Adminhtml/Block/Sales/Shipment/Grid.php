<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_shipment_grid');
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
        return 'Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection';
    }

    /**
     * Prepare and set collection of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Shipment'),
            'index' => 'increment_id',
            'type' => 'text',
            'header_css_class' => 'col-shipment-number',
            'column_css_class' => 'col-shipment-number'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Ship Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'header_css_class' => 'col-period',
            'column_css_class' => 'col-period'
        ));

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Order'),
            'index' => 'order_increment_id',
            'type' => 'text',
            'header_css_class' => 'col-order-number',
            'column_css_class' => 'col-order-number'
        ));

        $this->addColumn('order_created_at', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Order Date'),
            'index' => 'order_created_at',
            'type' => 'datetime',
            'header_css_class' => 'col-period',
            'column_css_class' => 'col-period'
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Ship-to Name'),
            'index' => 'shipping_name',
            'header_css_class' => 'col-memo',
            'column_css_class' => 'col-memo'
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Total Quantity'),
            'index' => 'total_qty',
            'type' => 'number',
            'header_css_class' => 'col-qty',
            'column_css_class' => 'col-qty'
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('Mage_Sales_Helper_Data')->__('View'),
                        'url' => array('base' => '*/sales_shipment/view'),
                        'field' => 'shipment_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('Mage_Sales_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('Mage_Sales_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if (!$this->_authorization->isAllowed(null)) {
            return false;
        }

        return $this->getUrl('*/sales_shipment/view',
            array(
                'shipment_id' => $row->getId(),
            )
        );
    }

    /**
     * Prepare and set options for massaction
     *
     * @return Mage_Adminhtml_Block_Sales_Shipment_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
            'label' => Mage::helper('Mage_Sales_Helper_Data')->__('PDF Packing Slips'),
            'url' => $this->getUrl('*/sales_shipment/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
            'label' => Mage::helper('Mage_Sales_Helper_Data')->__('Print Shipping Labels'),
            'url' => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    /**
     * Get url of grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true));
    }

}
