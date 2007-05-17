<?php
/**
 * Category data form
 *
 * @package    Mage_Admin
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Catalog_Block_Admin_Category_FormJson extends Varien_Data_Form
{
    /**
     * Constructor
     *
     */
    public function __construct() 
    {
        parent::__construct();
        $this->setId('add_child_category_form');
        $this->setAction(Mage::getBaseUrl().'mage_catalog/category/save/');
        
        $categoryId = (int) Mage::registry('controller')->getRequest()->getParam('category_id', false);
        $isNew = (bool) Mage::registry('controller')->getRequest()->getParam('isNew', false);

        $this->addField('category_id', 'hidden', array('name'=>'category_id', 'value'=>$categoryId));
        $this->addField('attribute_set_id', 'hidden', array('name'=>'attribute_set_id', 'value'=>1));
        
        $attributes = Mage::getModel('catalog', 'category_attribute_set')
            ->setAttributeSetId(1)
            ->getAttributes();
         
        foreach ($attributes as $attribute) {
            $elementId      = $attribute->getCode();
            $elementType    = $attribute->getDataInput();
            
            $elementConfig  = array();
            $elementConfig['name'] = 'attribute['.$attribute->getId().']';
            $elementConfig['label']= $attribute->getCode();
            $elementConfig['id']   = $attribute->getCode();
            $elementConfig['value']= '';
            $elementConfig['title']= $attribute->getCode();
            $elementConfig['validation']= '';
            
            $this->addField($elementId, $elementType, $elementConfig);
        }
        /*
        if ($categoryId) {
            $category = Mage::getModel('catalog','category')->load($categoryId);
            $this->setElementsValues($category->getData());
        }
        */
        $this->setFileupload(true);
        
        if( $isNew === false ) {
            $category = Mage::getModel('catalog', 'category')->load($categoryId);
            $data = $category->getData();
            $this->setTitle("Edit Category '{$data['name']}'");
            $this->setValues($data);
        } elseif( $isNew === true ) {
            $this->addField('parent_category_id', 'hidden', array('name'=>'parent_category_id', 'value'=>$categoryId));
            $this->setTitle("Add New Category");
        }
        //$this->addField('name', 'text', array('name'=>'name', 'id'=>'new_category_name', 'label'=>'Category name'));
    }
}
