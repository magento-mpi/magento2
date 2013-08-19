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
 * Adminhtml popular tags report grid block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Report_Popular_Grid extends Magento_Adminhtml_Block_Widget_Grid
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

        $collection = Mage::getResourceModel('Magento_Tag_Model_Resource_Reports_Collection')
            ->addPopularity($storeId)
            ->addStatusFilter(Magento_Tag_Model_Tag::STATUS_APPROVED);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>__('Tag'),
            'index'     =>'name',
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('taged', array(
            'header'    =>__('Popularity'),
            'index'     =>'popularity',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('action',
            array(
                'header'    => __('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => __('Show Details'),
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
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));
        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportPopularCsv', __('CSV'));
        $this->addExportType('*/*/exportPopularExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/tagDetail', array('id'=>$row->getTagId()));
    }
}
