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
 * Adminhtml customer grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Grid extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('Magento_Catalog_Model_Product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Magento_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Magento_Core_Model_AppInterface::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> __('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
                'header_css_class'  => 'col-id',
                'column_css_class'  => 'col-id'
        ));
        $this->addColumn('name',
            array(
                'header'=> __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'header_css_class'  => 'col-name',
                'column_css_class'  => 'col-name'
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> __('Name in %1', $store->getName()),
                    'index' => 'custom_name',
                    'header_css_class'  => 'col-name',
                    'column_css_class'  => 'col-name'
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> __('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('Magento_Catalog_Model_Product_Type')->getOptionArray(),
                'header_css_class'  => 'col-type',
                'column_css_class'  => 'col-type'
        ));

        $sets = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection')
            ->setEntityTypeFilter(Mage::getModel('Magento_Catalog_Model_Product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> __('Attribute Set'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
                'header_css_class'  => 'col-attr-name',
                'column_css_class'  => 'col-attr-name'
        ));

        $this->addColumn('sku',
            array(
                'header'=> __('SKU'),
                'width' => '80px',
                'index' => 'sku',
                'header_css_class'  => 'col-sku',
                'column_css_class'  => 'col-sku'
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> __('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'header_css_class'  => 'col-price',
                'column_css_class'  => 'col-price'
        ));

        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Magento_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> __('Quantity'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
                    'header_css_class'  => 'col-qty',
                    'column_css_class'  => 'col-qty'
            ));
        }

        $this->addColumn('visibility',
            array(
                'header'=> __('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('Magento_Catalog_Model_Product_Visibility')->getOptionArray(),
                'header_css_class'  => 'col-visibility',
                'column_css_class'  => 'col-visibility'
        ));

        $this->addColumn('status',
            array(
                'header'=> __('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('Magento_Catalog_Model_Product_Status')->getOptionArray(),
                'header_css_class'  => 'col-status',
                'column_css_class'  => 'col-status'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> __('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('Magento_Core_Model_Website')->getCollection()->toOptionHash(),
                    'header_css_class'  => 'col-websites',
                    'column_css_class'  => 'col-websites'
            ));
        }

        $this->addColumn('edit',
            array(
                'header'    => __('Edit'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => __('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'header_css_class'  => 'col-action',
                'column_css_class'  => 'col-action'
        ));

        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Magento_Rss')) {
            $this->addRssList('rss/catalog/notifystock', __('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> __('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => __('Are you sure?')
        ));

        $statuses = Mage::getSingleton('Magento_Catalog_Model_Product_Status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> __('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => __('Status'),
                         'values' => $statuses
                     )
             )
        ));

        if ($this->_authorization->isAllowed('Magento_Catalog::update_attributes')){
            $this->getMassactionBlock()->addItem('attributes', array(
                'label' => __('Update Attributes'),
                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
            ));
        }

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
}
