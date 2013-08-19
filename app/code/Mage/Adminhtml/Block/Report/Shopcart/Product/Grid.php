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
 * Adminhtml products in carts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Shopcart_Product_Grid extends Mage_Adminhtml_Block_Report_Grid_Shopcart
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {
        /** @var $collection Mage_Reports_Model_Resource_Quote_Collection */
        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Quote_Collection');
        $collection->prepareForProductsInCarts()
            ->setSelectCountSqlType(Mage_Reports_Model_Resource_Quote_Collection::SELECT_COUNT_SQL_TYPE_CART);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'align'     =>'right',
            'index'     =>'entity_id',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));

        $this->addColumn('name', array(
            'header'    =>__('Product'),
            'index'     =>'name',
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('price', array(
            'header'    =>__('Price'),
            'type'      =>'currency',
            'currency_code' => $currencyCode,
            'index'     =>'price',
            'renderer'  =>'Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Currency',
            'rate'          => $this->getRate($currencyCode),
            'header_css_class'  => 'col-price',
            'column_css_class'  => 'col-price'
        ));

        $this->addColumn('carts', array(
            'header'    =>__('Carts'),
            'align'     =>'right',
            'index'     =>'carts',
            'header_css_class'  => 'col-carts',
            'column_css_class'  => 'col-carts'
        ));

        $this->addColumn('orders', array(
            'header'    =>__('Orders'),
            'align'     =>'right',
            'index'     =>'orders',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', __('CSV'));
        $this->addExportType('*/*/exportProductExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id'=>$row->getEntityId()));
    }
}

