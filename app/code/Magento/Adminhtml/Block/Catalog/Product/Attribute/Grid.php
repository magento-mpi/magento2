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
 * Product attributes grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Attribute_Grid extends Magento_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare product attributes grid collection object
     *
     * @return Magento_Adminhtml_Block_Catalog_Product_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Attribute_Collection')
            ->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return Magento_Adminhtml_Block_Catalog_Product_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('is_visible', array(
            'header'=>__('Visible'),
            'sortable'=>true,
            'index'=>'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'align' => 'center',
        ), 'frontend_label');

        $this->addColumnAfter('is_global', array(
            'header'=>__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>__('Store View'),
                Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>__('Web Site'),
                Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>__('Global'),
            ),
            'align' => 'center',
        ), 'is_visible');

        $this->addColumn('is_searchable', array(
            'header'=>__('Searchable'),
            'sortable'=>true,
            'index'=>'is_searchable',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'align' => 'center',
        ), 'is_user_defined');

        $this->addColumnAfter('is_filterable', array(
            'header'=>__('Use in Layered Navigation'),
            'sortable'=>true,
            'index'=>'is_filterable',
            'type' => 'options',
            'options' => array(
                '1' => __('Filterable (with results)'),
                '2' => __('Filterable (no results)'),
                '0' => __('No'),
            ),
            'align' => 'center',
        ), 'is_searchable');

        $this->addColumnAfter('is_comparable', array(
            'header'=>__('Comparable'),
            'sortable'=>true,
            'index'=>'is_comparable',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'align' => 'center',
        ), 'is_filterable');

        return $this;
    }
}
