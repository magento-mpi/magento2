<?php
/**
 * Product attributes form
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Admin_Product_Form extends Mage_Core_Block_Form
{
    protected $_attributeSet;
    protected $_group;
    
    public function __construct() 
    {
        parent::__construct();

        $this->setViewName('Mage_Core', 'form');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'x-form');
        $this->setAttribute('action', '/');
        
        $groupId  = Mage_Core_Controller::getController()->getRequest()->getParam('group', false);
        if ($groupId) {
            $this->_group = Mage::getModel('catalog', 'product_attribute_group')->get($groupId);
            if ($this->_group) {
                $this->setAttribute('legend', $this->_group['product_attribute_group_code']);
            }
        }
        
        $attributes = Mage::getModel('catalog', 'product_attribute_group')->getAttributes($groupId);
        foreach ($attributes as $attribute) {
            $this->attribute2field($attribute);
        }
        //$this->_attributeSet    = Mage_Core_Controller::getController()->getRequest()->getParam('set', false);
        
        
        
        
/*        $this->addField('text1', 'text', array('name'=>'text1', 'id'=>'text1', 'value'=>11, 'label'=>'My field'));
        $this->addField('text3', 'select', array('name'=>'text3', 'id'=>'text3', 'value'=>11,'label'=>'Select field', 'values'=>array(0=>array('value'=>1, 'label'=>'1111111'))));*/
    }
    
    public function attribute2field($attribute)
    {
        $elementId      = $attribute['attribute_code'];
        $elementType    = $attribute['data_input'];
        $elementConfig  = array();
        $elementConfig['name'] = $attribute['attribute_code'];
        $elementConfig['label'] = $attribute['attribute_code'];
        $elementConfig['id']   = $attribute['attribute_code'];
        $elementConfig['value']= '';
        $elementConfig['title']= $attribute['attribute_code'];
        //$elementConfig['maxlength'] = '';
        //$elementConfig['size'] = '';

        $this->addField($elementId, $elementType, $elementConfig);
    }
}