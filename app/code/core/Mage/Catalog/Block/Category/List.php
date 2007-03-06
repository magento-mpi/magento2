<?php



/**
 *  Catalog Category List block
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Moshe Gurvich <moshe@varien.com>
 */

class Mage_Catalog_Block_Category_List extends Mage_Core_Block_Template
{
    function __construct($attributes = array())
    {
        parent::__construct($attributes);
        
        $this->setViewName('Mage_Catalog', 'list');
        $this->assign('base_url', Mage::getBaseUrl());
        $this->assign('cssClassName', 'category');
    }
    
    function loadCategories($parent)
    {
        $categories = Mage::getModel('catalog','categories')->getLevel($parent);
        $data  = array();
        foreach ($categories as $item) {
            $data[] = array(
                'title' => $item->getData('name'),
                'id'    => $item->getId(),
            );
        }
        $this->assign('data', $data);
    }
    
}// Class Mage_Core_Block_List END