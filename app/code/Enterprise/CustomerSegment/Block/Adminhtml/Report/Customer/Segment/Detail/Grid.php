<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segments Detail grid
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid parameters
     *
     * @param array $attributes
     */
    protected function _construct($attributes = array())
    {
        parent::_construct($attributes);
        $this->setId('segmentGrid')->setUseAjax(true);
    }

    /**
     * Instanitate collection and set required data joins
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection */
        $collection = Mage::getResourceModel('Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection');
        $collection->addNameToSelect()
            ->setViewMode($this->getCustomerSegment()->getViewMode())
            ->addSegmentFilter($this->getCustomerSegment())
            ->addWebsiteFilter(Mage::registry('filter_website_ids'))
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
            ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Customer Segment Getter
     *
     * @return Enterprise_CustomerSegment_Model_Segment
     */
    public function getCustomerSegment()
    {
        return Mage::registry('current_customer_segment');
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('grid_entity_id', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('ID'),
            'width'     => 50,
            'index'     => 'entity_id',
            'type'      => 'number',
        ));
        $this->addColumn('grid_name', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('grid_email', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Email'),
            'width'     => 150,
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('Mage_Customer_Model_Resource_Group_Collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('grid_group', array(
            'header'    =>  Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Group'),
            'width'     =>  100,
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('grid_telephone', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Telephone'),
            'width'     => 100,
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('grid_billing_postcode', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('ZIP'),
            'width'     => 90,
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('grid_billing_country_id', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Country'),
            'width'     => 100,
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('grid_billing_region', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('State/Province'),
            'width'     => 100,
            'index'     => 'billing_region',
        ));

        $this->addColumn('grid_customer_since', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer Since'),
            'width'     => 200,
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Ajax grid URL getter
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/customerGrid',
            array('segment_id' => Mage::registry('current_customer_segment')->getId()));
    }

    /**
     * Mock function to prevent grid row highlight
     *
     * @param $item
     * @return null
     */
    public function getRowUrl($item)
    {
        return null;
    }
}