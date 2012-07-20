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
 * Adminhtml product grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Review_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setRowClickCallback('review.gridRowClick');
        $this->setUseAjax(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
                'header'    => Mage::helper('Mage_Review_Helper_Data')->__('ID'),
                'width'     => '50px',
                'index'     => 'entity_id',
        ));

        $this->addColumn('name', array(
                'header'    => Mage::helper('Mage_Review_Helper_Data')->__('Name'),
                'index'     => 'name',
        ));

        if ((int)$this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name', array(
                    'header'    => Mage::helper('Mage_Review_Helper_Data')->__('Name in Store'),
                    'index'     => 'custom_name'
            ));
        }

        $this->addColumn('sku', array(
                'header'    => Mage::helper('Mage_Review_Helper_Data')->__('SKU'),
                'width'     => '80px',
                'index'     => 'sku'
        ));

        $this->addColumn('price', array(
                'header'    => Mage::helper('Mage_Review_Helper_Data')->__('Price'),
                'type'      => 'currency',
                'index'     => 'price'
        ));

        $this->addColumn('qty', array(
                'header'    => Mage::helper('Mage_Review_Helper_Data')->__('Qty'),
                'width'     => '130px',
                'type'      => 'number',
                'index'     => 'qty'
        ));

        $this->addColumn('status', array(
                'header'    => Mage::helper('Mage_Review_Helper_Data')->__('BugsCoverage'),
                'width'     => '90px',
                'index'     => 'status',
                'type'      => 'options',
                'source'    => 'Mage_Catalog_Model_Product_Status',
                'options'   => Mage::getSingleton('Mage_Catalog_Model_Product_Status')->getOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('Mage_Review_Helper_Data')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('Mage_Core_Model_Website')->getCollection()->toOptionHash(),
            ));
        }
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/jsonProductInfo', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        return $this;
    }
}
