<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle selection product grid
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('bundle_selection_search_grid');
        $this->setRowClickCallback('bSelection.productGridRowClick.bind(bSelection)');
        $this->setCheckboxCheckCallback('bSelection.productGridCheckboxCheck.bind(bSelection)');
        $this->setRowInitCallback('bSelection.productGridRowInit.bind(bSelection)');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid filter buttons
     */
    protected function _prepareFilterButtons()
    {
        $this->getChildBlock('reset_filter_button')->setData(
            'onclick',
            $this->getJsObjectName() . '.resetFilter(bSelection.gridUpdateCallback)'
        );
        $this->getChildBlock('search_button')->setData(
            'onclick',
            $this->getJsObjectName() . '.doFilter(bSelection.gridUpdateCallback)'
        );
    }

    protected function _beforeToHtml()
    {
        $this->setId($this->getId() . '_' . $this->getIndex());
        return parent::_beforeToHtml();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Catalog_Model_Product')->getCollection()
            ->setOrder('id')
            ->setStore($this->getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToFilter('type_id', array('in' => $this->getAllowedSelectionTypes()))
            ->addFilterByRequiredOptions()
            ->addStoreFilter();

        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText($this->__('Please enter search conditions to view products.'));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('is_selected', array(
            'type'      => 'checkbox',
            'name'      => 'in_selected',
            'align'     => 'center',
            'values'    => $this->_getSelectedProducts(),
            'index'     => 'entity_id',
            'header_css_class'=> 'col-select',
            'column_css_class'=> 'col-select'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Product Name'),
            'index'     => 'name',
            'header_css_class'=> 'col-name',
            'column_css_class'=> 'name col-name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
            'header_css_class'=> 'col-sku',
            'column_css_class'=> 'sku col-sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'header_css_class'=> 'col-price',
            'column_css_class'=> 'col-price'
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/bundle_selection/grid', array('index' => $this->getIndex(), 'productss' => implode(',', $this->_getProducts())));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost(
            'selected_products',
            explode(',', $this->getRequest()->getParam('productss'))
        );
        return $products;
    }

    protected function _getProducts()
    {
        if ($products = $this->getRequest()->getPost('products', null)) {
            return $products;
        } else if ($productss = $this->getRequest()->getParam('productss', null)) {
            return explode(',', $productss);
        } else {
            return array();
        }
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Retrieve array of allowed product types for bundle selection product
     *
     * @return array
     */
    public function getAllowedSelectionTypes()
    {
        return Mage::helper('Mage_Bundle_Helper_Data')->getAllowedSelectionTypes();
    }
}
