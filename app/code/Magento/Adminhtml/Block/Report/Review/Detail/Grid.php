<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml report reviews product grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Review_Detail_Grid extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('reviews_grid');
    }

    protected function _prepareCollection()
    {

        //$collection = Mage::getModel('Magento_Review_Model_Review')->getProductCollection();

        //$collection->getSelect()
        //    ->where('rt.entity_pk_value='.(int)$this->getRequest()->getParam('id'));

        //$collection->getEntity()->setStore(0);

        $collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Review_Collection')
            ->addProductFilter((int)$this->getRequest()->getParam('id'));

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {

        $this->addColumn('nickname', array(
            'header'    =>__('Customer'),
            'width'     =>'100px',
            'index'     =>'nickname'
        ));

        $this->addColumn('title', array(
            'header'    =>__('Title'),
            'width'     =>'150px',
            'index'     =>'title'
        ));

        $this->addColumn('detail', array(
            'header'    =>__('Detail'),
            'index'     =>'detail'
        ));

        $this->addColumn('created_at', array(
            'header'    =>__('Created'),
            'index'     =>'created_at',
            'width'     =>'200px',
            'type'      =>'datetime'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductDetailCsv', __('CSV'));
        $this->addExportType('*/*/exportProductDetailExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

}

