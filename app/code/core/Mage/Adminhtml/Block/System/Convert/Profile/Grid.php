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
 * Convert profiles grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('convertProfileGrid');
        $this->setDefaultSort('profile_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Dataflow_Model_Resource_Profile_Collection')
            ->addFieldToFilter('entity_type', array('null'=>''));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('profile_id', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('ID'),
            'width'     => '50px',
            'index'     => 'profile_id',
        ));
        $this->addColumn( 'name', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profile Name'),
            'index'     => 'name',
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Created At'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     => 'created_at',
        ));
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Updated At'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     => 'updated_at',
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Action'),
            'width'     => '60px',
            'align'     => 'center',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'action',
            'actions'   => array(
                array(
                    'url'       => $this->getUrl('*/*/edit') . 'id/$profile_id',
                    'caption'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Edit')
                )
            )
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}

