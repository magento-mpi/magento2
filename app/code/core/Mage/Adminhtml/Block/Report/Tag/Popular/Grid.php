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
 * Adminhtml popular tags report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Popular_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {

        if ($this->getRequest()->getParam('website')) {
            $storeId = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } else if ($this->getRequest()->getParam('group')) {
            $storeId = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Tag_Collection')
            ->addPopularity($storeId)
            ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Tag Name'),
            'index'     =>'name'
        ));

        $this->addColumn('taged', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Popularity'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'popularity'
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Action'),
                'width'     => '100%',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('Mage_Catalog_Helper_Data')->__('Show Details'),
                        'url'     => array(
                            'base'=>'*/*/tagDetail'
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

        $this->addExportType('*/*/exportPopularCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportPopularExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/tagDetail', array('id'=>$row->getTagId()));
    }

}
