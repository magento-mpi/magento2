<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle selection product grid
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid
    extends Magento_Adminhtml_Block_Widget_Grid
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

    /**
     * Initialize grid before rendering
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->setId($this->getId() . '_' . $this->getIndex());
        return parent::_beforeToHtml();
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Magento_Catalog_Model_Product')->getCollection()
            ->setOrder('id')
            ->setStore($this->getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToFilter('entity_id', array('nin' => $this->_getSelectedProducts()))
            ->addAttributeToFilter('type_id', array('in' => $this->getAllowedSelectionTypes()))
            ->addFilterByRequiredOptions()
            ->addStoreFilter();

        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText(__('Please enter search conditions to view products.'));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Initialize grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => __('ID'),
                'index' => 'entity_id',
                'renderer' => 'Magento_Backend_Block_Widget_Grid_Column_Renderer_Checkbox',
                'type' => 'skip-list'
            )
        );

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'index'     => 'name',
            'header_css_class'=> 'col-name',
            'column_css_class'=> 'name col-name'
        ));
        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
            'header_css_class'=> 'col-sku',
            'column_css_class'=> 'sku col-sku'
        ));
        $this->addColumn('price', array(
            'header'    => __('Price'),
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

    /**
     * Retrieve grid reload url
     *
     * @return string
     */
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
        return Mage::helper('Magento_Bundle_Helper_Data')->getAllowedSelectionTypes();
    }
}
