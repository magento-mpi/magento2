<?php
/**
 * Product attributes form
 *
 * @package    Mage_Admin
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Admin_Product_FormJson extends Varien_Data_Form
{
    protected $_dataInputs;
    protected $_dataSources;
    
    public function __construct() 
    {
        parent::__construct();
        // Request params
        $groupId  = Mage::registry('controller')->getRequest()->getParam('group', false);
        $setId    = Mage::registry('controller')->getRequest()->getParam('set', false);
        $productId= (int) Mage::registry('controller')->getRequest()->getParam('product', false);
        $isDefault= (bool) Mage::registry('controller')->getRequest()->getParam('isdefault', false);

        // Config settings
        $this->_dataInputs = (array) Mage::getConfig()->getNode('admin/dataInputs');

        // Set form attributes
        $postUrl = Mage::getBaseUrl().'mage_catalog/product/save/';
        if ($productId) {
            $postUrl.= 'product/'.$productId;
        }
        
        $this->setMethod('POST');
        $this->setClass('x-form');
        $this->setAcrion($postUrl);
        

        $group = Mage::getModel('catalog', 'product_attribute_group')->load($groupId);
        $fieldset = $this->addFieldset($group->getCode(), array('legend'=>$group->getCode()));
        
        if ($setId) {
            $this->addField('set_id', 'hidden', array('name'=>'set_id', 'value'=>$setId));
        }
        
        $attributes = $group->getAttributes();
        foreach ($attributes as $attribute) {
            $this->attribute2field($attribute, $fieldset);
        }
        
        if ($productId) {
            $product = Mage::getModel('catalog','product')->load($productId);
            $productInfo = $product->getData();
            $this->setValues($productInfo);
        }
    }
    
    public function attribute2field($attribute, $fieldset)
    {
        $elementId      = $attribute->getCode();
        $elementType    = $attribute->getDataInput();
        
        $elementConfig  = array();
        $elementConfig['name'] = 'attribute['.$attribute->getId().']';
        $elementConfig['label']= $attribute->getCode();
        $elementConfig['id']   = $attribute->getCode();
        $elementConfig['title']= $attribute->getCode();
        
        // Parse input element params
        if (isset($this->_dataInputs[$attribute->getDataInput()])) {
            $htmlParams = (array) $this->_dataInputs[$attribute->getDataInput()];
            $htmlParams = isset($htmlParams['htmlParams']) ? (array) $htmlParams['htmlParams'] : array();
            foreach ($htmlParams as $paramName=>$paramValue) {
                if (!isset($elementConfig[$paramName])) {
                    $elementConfig[$paramName] = $paramValue;
                }
            }
        }
        
        if ($source = $attribute->getSource()) {
            $elementConfig['values'] = $source->getArrOptions();
        }
        $fieldset->addField($elementId, $elementType, $elementConfig);
    }
}