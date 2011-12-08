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
 * Adminhtml most viewed products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Viewed_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridViewedProducts');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('Mage_Reports_Model_Resource_Product_Viewed_Collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Product Name'),
            'index'     =>'name',
            'total'     =>Mage::helper('Mage_Reports_Helper_Data')->__('Subtotal')
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('Mage_Reports_Helper_Data')->__('Price'),
            'width'         => '120px',
            'type'          => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'index'         => 'price',
        ));

        $this->addColumn('views', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number of Views'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'views',
            'total'     =>'sum'
        ));

        $this->addExportType('*/*/exportViewedCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportViewedExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}
