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
 * Product in category grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('super_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        if ($this->_getProduct() && $this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/superGroup', array('_current'=>true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Retrieve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group
     */
    protected function _prepareCollection()
    {
        $allowProductTypes = array();
        $allowProductTypeNodes = Mage::getConfig()
            ->getNode('global/catalog/product/type/grouped/allow_product_types')->children();
        foreach ($allowProductTypeNodes as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $collection = Mage::getModel('Mage_Catalog_Model_Product_Link')->useGroupedLinks()
            ->getProductCollection()
            ->setProduct($this->_getProduct())
            ->addAttributeToSelect('*')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowProductTypes);

        if ($this->getIsReadonly() === true) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Price'),
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        $this->addColumn('qty', array(
            'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Default Qty'),
            'name'      => 'qty',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
            'editable'  => true
        ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Position'),
            'name'      => 'position',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getProduct()->getId()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url')
            ? $this->_getData('grid_url') : $this->getUrl('*/*/superGroupGridOnly', array('_current'=>true));
    }

    /**
     * Retrieve selected grouped products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsGrouped();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedGroupedProducts());
        }
        return $products;
    }

    /**
     * Retrieve grouped products
     *
     * @return array
     */
    public function getSelectedGroupedProducts()
    {
        $associatedProducts = Mage::registry('current_product')->getTypeInstance()
            ->getAssociatedProducts(Mage::registry('current_product'));
        $products = array();
        foreach ($associatedProducts as $product) {
            $products[$product->getId()] = array(
                'qty'       => $product->getQty(),
                'position'  => $product->getPosition()
            );
        }
        return $products;
    }

    public function getTabLabel()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Associated Products');
    }
    public function getTabTitle()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Associated Products');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
}
