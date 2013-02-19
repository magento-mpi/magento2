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
 * Adminhtml product downloads report grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Downloads_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('downloadsGrid');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Product_Downloads_Collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addAttributeToFilter('type_id', array(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE))
            ->addSummary();

        if( $storeId ) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Product Name'),
            'index'     => 'name'
        ));

        $this->addColumn('link_title', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Link'),
            'index'     => 'link_title'
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Product SKU'),
            'index'     =>'sku'
        ));

        $this->addColumn('purchases', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Purchases'),
            'width'     => '215px',
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'purchases',
            'type'      => 'number',
            'renderer'  => 'Mage_Adminhtml_Block_Report_Product_Downloads_Renderer_Purchases',
        ));

        $this->addColumn('downloads', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Downloads'),
            'width'     => '215px',
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'downloads',
            'type'      => 'number'
        ));

        $this->addExportType('*/*/exportDownloadsCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportDownloadsExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
