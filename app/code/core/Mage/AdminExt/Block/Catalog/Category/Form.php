<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category data form
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Block_Catalog_Category_Form extends Varien_Data_Form
{
    /**
     * Constructor
     *
     */
    public function __construct() 
    {
        parent::__construct();
        $this->setId('add_child_category_form');
        $this->setAction(Mage::getBaseUrl().'admin/category/save/');
        
        $categoryId = (int) Mage::registry('controller')->getRequest()->getParam('category_id', false);
        $isNew = (bool) Mage::registry('controller')->getRequest()->getParam('isNew', false);

        $this->addField('category_id', 'hidden', array('name'=>'category_id', 'value'=>$categoryId));
        $this->addField('attribute_set_id', 'hidden', array('name'=>'attribute_set_id', 'value'=>1));
        
        $attributes = Mage::getModel('catalog/category_attribute_set')
            ->setAttributeSetId(1)
            ->getAttributes();
         
        foreach ($attributes as $attribute) {
            $elementId      = $attribute->getCode();
            $elementType    = $attribute->getDataInput();
            
            $elementConfig  = array();
            $elementConfig['name'] = $attribute->getFormFieldName();
            $elementConfig['label']= __($attribute->getCode());
            $elementConfig['id']   = $attribute->getCode();
            $elementConfig['value']= '';
            $elementConfig['title']= $attribute->getCode();
            $elementConfig['validation']= '';
            
            $this->addField($elementId, $elementType, $elementConfig);
        }

        $this->setFileupload(true);
        
        if( $isNew === false ) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
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
