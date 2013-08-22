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
 * Adminhtml sales order create search products block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Promo_Widget_Chooser_Sku extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('skuChooserGrid_'.$this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("$form.chooserGridRowClick.bind($form)");
        $this->setCheckboxCheckCallback("$form.chooserGridCheckboxCheck.bind($form)");
        $this->setRowInitCallback("$form.chooserGridRowInit.bind($form)");
        $this->setDefaultSort('sku');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $selected = $this->_getSelectedProducts();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('sku', array('in'=>$selected));
            } else {
                $this->getCollection()->addFieldToFilter('sku', array('nin'=>$selected));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @return Magento_Adminhtml_Block_Promo_Widget_Chooser_Sku
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Collection')
            ->setStoreId(0)
            ->addAttributeToSelect('name', 'type_id', 'attribute_set_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define Cooser Grid Columns and filters
     *
     * @return Magento_Adminhtml_Block_Promo_Widget_Chooser_Sku
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'sku',
            'use_index' => true,
        ));

        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));

        $this->addColumn('type',
            array(
                'header'=> __('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('Magento_Catalog_Model_Product_Type')->getOptionArray(),
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
        ));

        $this->addColumn('chooser_sku', array(
            'header'    => __('SKU'),
            'name'      => 'chooser_sku',
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('chooser_name', array(
            'header'    => __('Product'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/chooser', array(
            '_current'          => true,
            'current_grid_id'   => $this->getId(),
            'collapse'          => null
        ));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected', array());

        return $products;
    }

}

