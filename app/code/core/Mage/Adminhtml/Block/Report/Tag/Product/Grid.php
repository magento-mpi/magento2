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
 * Adminhtml tags by products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Tag_Product_Collection');

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
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('ID'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Product Name'),
            'index'     =>'name'
        ));

        $this->addColumn('utaged', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number of Unique Tags'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'utaged'
        ));

        $this->addColumn('taged', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number of Total Tags'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'taged'
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Action'),
                'width'     => '100%',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('Mage_Catalog_Helper_Data')->__('Show Tags'),
                        'url'     => array(
                            'base'=>'*/*/productDetail'
                        ),
                        'field'   => 'id'
                    )
                ),
                'is_system' => true,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportProductExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/productDetail', array('id'=>$row->getId()));
    }

}
