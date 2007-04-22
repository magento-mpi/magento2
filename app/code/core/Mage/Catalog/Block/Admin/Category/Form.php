<?php
/**
 * Category data form
 *
 * @package    Mage_Admin
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Admin_Category_Form extends Mage_Core_Block_Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('form.phtml');
        $this->setAttribute('id', 'add_child_category_form');
        $this->setAttribute('legend', 'Category form');
        $this->setAttribute('class', 'x-form');
        $this->setAttribute('action', Mage::getBaseUrl().'/mage_catalog/category/save/');
        
        $parentId = Mage::registry('controller')->getRequest()->getParam('parent', false);
        $this->addField('parent', 'hidden', array('name'=>'parent', 'value'=>$parentId));
        $this->addField('name', 'text', array('name'=>'name', 'id'=>'new_category_name', 'label'=>'Category name'));
    }
}