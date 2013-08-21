<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segments Detail grid
 *
 * @category   Magento
 * @package    Magento_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid
    extends Magento_Adminhtml_Block_Widget_Grid
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
     * @return Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Magento_CustomerSegment_Model_Resource_Report_Customer_Collection */
        $collection = Mage::getResourceModel('Magento_CustomerSegment_Model_Resource_Report_Customer_Collection');
        $collection->addNameToSelect()
            ->setViewMode($this->getCustomerSegment()->getViewMode())
            ->addSegmentFilter($this->getCustomerSegment())
            ->addWebsiteFilter(Mage::registry('filter_website_ids'))
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Customer Segment Getter
     *
     * @return Magento_CustomerSegment_Model_Segment
     */
    public function getCustomerSegment()
    {
        return Mage::registry('current_customer_segment');
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('grid_entity_id', array(
            'header'    => __('ID'),
            'index'     => 'entity_id',
            'type'      => 'number',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));
        $this->addColumn('grid_name', array(
            'header'    => __('Name'),
            'index'     => 'name',
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));
        $this->addColumn('grid_email', array(
            'header'    => __('Email'),
            'index'     => 'email',
            'header_css_class'  => 'col-mail',
            'column_css_class'  => 'col-mail'
        ));

        $groups = Mage::getResourceModel('Magento_Customer_Model_Resource_Group_Collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('grid_group', array(
            'header'    =>  __('Group'),
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
            'header_css_class'  => 'col-group',
            'column_css_class'  => 'col-group'
        ));

        $this->addColumn('grid_telephone', array(
            'header'    => __('Phone'),
            'index'     => 'billing_telephone',
            'header_css_class'  => 'col-phone',
            'column_css_class'  => 'col-phone'
        ));

        $this->addColumn('grid_billing_postcode', array(
            'header'    => __('ZIP'),
            'index'     => 'billing_postcode',
            'header_css_class'  => 'col-zip',
            'column_css_class'  => 'col-zip'
        ));

        $this->addColumn('grid_billing_country_id', array(
            'header'    => __('Country'),
            'type'      => 'country',
            'index'     => 'billing_country_id',
            'header_css_class'  => 'col-country',
            'column_css_class'  => 'col-country'
        ));

        $this->addColumn('grid_billing_region', array(
            'header'    => __('State/Province'),
            'index'     => 'billing_region',
            'header_css_class'  => 'col-state',
            'column_css_class'  => 'col-state'
        ));

        $this->addColumn('grid_customer_since', array(
            'header'    => __('Customer Since'),
            'type'      => 'datetime',
            'index'     => 'created_at',
            'gmtoffset' => true,
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportExcel', __('Excel XML'));

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
