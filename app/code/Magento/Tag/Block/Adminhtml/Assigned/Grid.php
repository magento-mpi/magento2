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
 * Adminhtml assigned products grid block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Assigned_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    protected $_currentTagModel;

    /**
     * Set grid params
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_currentTagModel = Mage::registry('current_tag');
        $this->setId('tag_assigned_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->_getTagId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }

    /**
     * Tag ID getter
     *
     * @return int
     */
    protected function _getTagId()
    {
        return $this->_currentTagModel ? $this->_currentTagModel->getId() : null;
    }

    /**
     * Store getter
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * Add filter to grid columns
     *
     * @param mixed $column
     * @return Magento_Tag_Block_Adminhtml_Assigned_Grid
     */
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
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Retrieve Products Collection
     *
     * @return Magento_Tag_Block_Adminhtml_Assigned_Grid
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('Magento_Catalog_Model_Product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            //->addAttributeToFilter('status', array(''))
            ->joinField('qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');

        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    /**
     * Prepeare columns for grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'field_name'        => 'in_products',
            'values'            => $this->_getSelectedProducts(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));

        $this->addColumn('entity_id',
            array(
                'header'=> __('ID'),
                'width' => 50,
                'sortable'  => true,
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> __('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> __('Name in %1', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'    => __('Type'),
                'width'     => 100,
                'index'     => 'type_id',
                'type'      => 'options',
                'options'   => Mage::getSingleton('Magento_Catalog_Model_Product_Type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection')
            ->setEntityTypeFilter(Mage::getModel('Magento_Catalog_Model_Product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'    => __('Attribute Set'),
                'width'     => 100,
                'index'     => 'attribute_set_id',
                'type'      => 'options',
                'options'   => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> __('SKU'),
                'width' => 80,
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'        => __('Price'),
                'type'          => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index'         => 'price',
        ));

        $this->addColumn('visibility',
            array(
                'header'    => __('Visibility'),
                'width'     => 100,
                'index'     => 'visibility',
                'type'      => 'options',
                'options'   => Mage::getModel('Magento_Catalog_Model_Product_Visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'    => __('Status'),
                'width'     => 70,
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('Magento_Catalog_Model_Product_Status')->getOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('assigned_products', null);
        if (!is_array($products)) {
            $products = $this->getRelatedProducts();
        }
        return $products;
    }

    /**
     * Retrieve Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/assignedGridOnly', array('_current' => true));
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getRelatedProducts()
    {
        return $this->_currentTagModel
            ->setStatusFilter(Magento_Tag_Model_Tag::STATUS_APPROVED)
            ->getRelatedProductIds();
    }
}
