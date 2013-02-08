<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tags by products report grid block
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Block_Adminhtml_Report_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {
        /** @var $collection Mage_Tag_Model_Resource_Reports_Product_Collection */
        $collection = Mage::getResourceModel('Mage_Tag_Model_Resource_Reports_Product_Collection');

        $collection->addUniqueTagedCount()
            ->addAllTagedCount()
            ->addStatusFilter(Mage::getModel('Mage_Tag_Model_Tag')->getApprovedStatus())
            ->addGroupByProduct();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'            => Mage::helper('Mage_Tag_Helper_Data')->__('ID'),
            'index'             => 'entity_id',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));

        $this->addColumn('name', array(
            'header'            => Mage::helper('Mage_Tag_Helper_Data')->__('Product Name'),
            'index'             => 'name',
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $this->addColumn('utaged', array(
            'header'    =>Mage::helper('Mage_Tag_Helper_Data')->__('Number of Unique Tags'),
            'index'             => 'utaged',
            'header_css_class'  => 'col-unique-numbers',
            'column_css_class'  => 'col-unique-numbers'
        ));

        $this->addColumn('taged', array(
            'header'    =>Mage::helper('Mage_Tag_Helper_Data')->__('Number of Total Tags'),
            'index'             => 'taged',
            'header_css_class'  => 'col-total-numbers',
            'column_css_class'  => 'col-total-numbers'
        ));

        $this->addColumn('action',
            array(
                'header'            => Mage::helper('Mage_Tag_Helper_Data')->__('Action'),
                'type'              => 'action',
                'getter'            => 'getId',
                'actions'           => array(
                    array(
                        'caption' => Mage::helper('Mage_Tag_Helper_Data')->__('Show Tags'),
                        'url'     => array(
                            'base'=>'*/*/productDetail'
                        ),
                        'field'   => 'id'
                    )
                ),
                'is_system'         => true,
                'filter'            => false,
                'sortable'          => false,
                'index'             => 'stores',
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', Mage::helper('Mage_Tag_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportProductExcel', Mage::helper('Mage_Tag_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/productDetail', array('id'=>$row->getId()));
    }

}
