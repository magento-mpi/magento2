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
    function loadCategories($parent)
    {
        $nodes = Mage::getModel('catalog_resource/category_tree')
            ->joinAttribute('name')
            ->load($parent)
            ->getNodes();

        $this->assign('categories', $nodes);
    }
    
    public function loadProductManufacturers()
    {
        $manufacturers = Mage::getModel('catalog/product_attribute')
            ->loadByCode('manufacturer')
            ->getSource()
                ->getArrOptions();

        $this->assign('manufacturers', $manufacturers);
    }
    
    public function loadProductTypes()
    {
        $types = Mage::getModel('catalog/product_attribute')
            ->loadByCode('shoe_type')
            ->getSource()
                ->getArrOptions();

        $this->assign('types', $types);
    }    
}// Class Mage_Core_Block_List END