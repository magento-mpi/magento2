<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml all tags grid block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Grid_All extends Magento_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('tagsGrid');
        $this->setDefaultSort('tag_id', 'desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Tag_Model_Resource_Tag_Collection')
//            ->addStoreFilter(Mage::app()->getStore()->getId())
               ->addStoresVisibility()
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('Magento_Tag_Helper_Data')->__('Tag'),
            'index'     => 'name',
        ));
        $this->addColumn('total_used', array(
            'header'    => Mage::helper('Magento_Tag_Helper_Data')->__('Uses'),
            'width'     => '140px',
            'align'     => 'center',
            'index'     => 'total_used',
            'type'      => 'number',
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('Magento_Tag_Helper_Data')->__('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'    => array(
                Magento_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('Magento_Tag_Helper_Data')->__('Disabled'),
                Magento_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('Magento_Tag_Helper_Data')->__('Pending'),
                Magento_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('Magento_Tag_Helper_Data')->__('Approved'),
            ),
        ));



        $this->setColumnFilter('id')
            ->setColumnFilter('name')
            ->setColumnFilter('total_used')
        ;

        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            if($column->getIndex()=='stores') {
                $this->getCollection()->addAttributeToFilter( $column->getIndex(), $column->getFilter()->getCondition());
            } else {
                $this->getCollection()->addStoreFilter($column->getFilter()->getCondition());
            }
        }
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/products', array('tag_id' => $row->getId()));
    }

}
