<?php
/**
 * Adminhtml tagginf customers grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag_Grid_Customers extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag_customer/collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
//            ->addAttributeToSelect('email')
//            ->addAttributeToSelect('created_at')
//            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing')
//            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing')
//            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing')
//            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing')
//            ->joinField('billing_country_name', 'directory/country_name', 'name', 'country_id=billing_country_id', array('language_code'=>'en'))
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>__('ID'),
            'width'     => '40px',
            'align'     =>'center',
            'sortable'  =>true,
            'index'     =>'entity_id'
        ));
        $this->addColumn('firstname', array(
            'header'    =>__('First Name'),
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>__('Last Name'),
            'index'     =>'lastname'
        ));
//        $this->addColumn('email', array(
//            'header'    =>__('Email'),
//            'align'     =>'center',
//            'index'     =>'email'
//        ));
//        $this->addColumn('Telephone', array(
//            'header'    =>__('Telephone'),
//            'align'     =>'center',
//            'index'     =>'billing_telephone'
//        ));
//        $this->addColumn('billing_postcode', array(
//            'header'    =>__('Postal Code'),
//            'index'     =>'billing_postcode',
//        ));
//        $this->addColumn('billing_country_name', array(
//            'header'    =>__('Country'),
//            #'filter'    => 'adminhtml/customer_grid_filter_country',
//            'index'     =>'billing_country_name',
//        ));
//        $this->addColumn('customer_since', array(
//            'header'    =>__('Customer Since'),
//            'type'      => 'date',
//            'align'     => 'center',
//            #'format'    => 'Y.m.d',
//            'index'     =>'created_at',
//        ));
        $this->addColumn('tags', array(
            'header'    => __('Tags'),
            'index'     => 'tags',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'adminhtml/tag_grid_column_renderer_tags'
        ));
        $this->addColumn('action', array(
            'header'    =>__('Action'),
            'align'     =>'center',
            'width'     => '120px',
            'format'    =>'<a href="'.Mage::getUrl('*/*/products/customer_id/$entity_id').'">'.__('View Products').'</a>',
            'filter'    =>false,
            'sortable'  =>false,
            'is_system' =>true
        ));

        $this->setColumnFilter('id')
            ->setColumnFilter('email')
            ->setColumnFilter('firstname')
            ->setColumnFilter('lastname');

//        $this->addExportType('*/*/exportCsv', __('CSV'));
//        $this->addExportType('*/*/exportXml', __('XML'));
        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }
        return $this;
    }

}