<?php
/**
 * Catalog navigation
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Navigation extends Mage_Core_Block_Template
{
    function __construct($attributes = array())
    {
        parent::__construct($attributes);
        
        //$this->assign('base_url', Mage::getBaseUrl());
    }
    
    function loadCategories($parent)
    {
        $categoryTree = Mage::getModel('catalog','category_tree')->getLevel($parent);
        $data  = array();
        foreach ($categoryTree as $item) {
            $data[] = array(
                'title' => $item->getData('attribute_value'),
                'id'    => $item->getId(),
            );
        }
        $this->assign('categories', $data);
    }
    
    public function loadProductManufacturers()
    {
        $manufacturers = Mage::getModel('catalog','product_attribute_option')->getOptions(array('option_type'=>'manufacturer'));
        $this->assign('manufacturers', $manufacturers);
    }
    
    public function loadProductTypes()
    {
        $types = Mage::getModel('catalog','product_attribute_option')->getOptions(array('option_type'=>'type'));
        $this->assign('types', $types);
    }    
}// Class Mage_Core_Block_List END