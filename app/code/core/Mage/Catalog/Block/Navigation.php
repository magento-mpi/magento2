<?php
/**
 * Catalog navigation
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Navigation extends Mage_Core_Block_Template
{
    protected function _loadCategories($parent)
    {
        $nodes = Mage::getResourceModel('catalog/category_tree')
            ->joinAttribute('name')
            ->load($parent)
            ->getNodes();

        $this->assign('categories', $nodes);
    }
    
    protected function _loadProductManufacturers()
    {
        $manufacturers = Mage::getModel('catalog/product_attribute')
            ->loadByCode('manufacturer')
            ->getSource()
                ->getArrOptions();

        $this->assign('manufacturers', $manufacturers);
    }
    
    protected function _loadProductTypes()
    {
        $types = Mage::getModel('catalog/product_attribute')
            ->loadByCode('shoe_type')
            ->getSource()
                ->getArrOptions();

        $this->assign('types', $types);
    }
    
    protected function _beforeToHtml()
    {
        $this->_loadCategories($this->getCategoriesParentId());
        $this->_loadProductManufacturers();
        $this->_loadProductTypes();
        return true;
    }
}// Class Mage_Core_Block_List END