<?php

#include_once 'Ecom/Core/Block/Template.php';

/**
 *  Catalog Category List block
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Moshe Gurvich <moshe@varien.com>
 */

class Ecom_Catalog_Block_Category_List extends Ecom_Core_Block_Template
{
    function __construct($attributes = array())
    {
        parent::__construct($attributes);
        
        $this->setViewName('Ecom_Catalog', 'list');
        $this->assign('base_url', Ecom::getBaseUrl());
        $this->assign('cssClassName', 'category');
    }
    
    function loadCategories($parent)
    {
        $categories = Ecom::getModel('catalog','categories')->getLevel($parent);
        $data  = array();
        foreach ($categories as $item) {
            $data[] = array(
                'title' => $item->getData('name'),
                'id'    => $item->getId(),
            );
        }
        $this->assign('data', $data);
    }
    
}// Class Ecom_Core_Block_List END