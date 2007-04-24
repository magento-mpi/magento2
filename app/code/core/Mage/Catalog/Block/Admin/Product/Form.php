<?php
/**
 * Product attributes form
 *
 * @package    Mage_Admin
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Admin_Product_Form extends Mage_Core_Block_Form
{
    protected $_dataInputs;
    protected $_dataSources;
    
    public function __construct() 
    {
        parent::__construct();
        // Config settings
        $this->_dataInputs = (array) Mage::getConfig()->getXml('admin/dataInputs');
        $this->_dataSources= (array) Mage::getConfig()->getXml('modules/Mage_Catalog/admin/dataSources');

        $this->setTemplate('form.phtml');
        
        // Set form attributes
        $this->setAttribute('method', 'POST');
        $this->setAttribute('class', 'x-form');
        $this->setAttribute('action', Mage::getBaseUrl().'/mage_catalog/product/save/');
        
        // Request params
        $groupId  = Mage::registry('controller')->getRequest()->getParam('group', false);
        $setId    = Mage::registry('controller')->getRequest()->getParam('set', false);
        $productId= (int) Mage::registry('controller')->getRequest()->getParam('product', false);
        $isDefault= (bool) Mage::registry('controller')->getRequest()->getParam('isdefault', false);

        $group = Mage::getModel('catalog', 'product_attribute_group')->load($groupId);
        $this->setAttribute('legend', $group->getCode());
        $this->setAttribute('id', 'form_'.$groupId);
        
        if ($isDefault) {
            $this->addField('product_id', 'hidden',
                array(
                    'name'  => 'product_id',
                    'value' => $productId,
                    'id'    => 'product_id'
                )
            );
            $this->addField('attribute_set_id', 'hidden',
                array(
                    'name'  => 'set_id',
                    'value' => $setId,
                    'id'    => 'set_id'
                )
            );
        }
        
        $attributes = $group->getAttributesBySet($setId);
        foreach ($attributes as $attribute) {
            $this->attribute2field($attribute);
        }
        
        if ($productId) {
            $product = Mage::getModel('catalog','product')->load($productId);
            $productInfo = $product->getData();
            $this->setElementsValues($productInfo);
        }
    }
    
    public function attribute2field($attribute)
    {
        $elementId      = $attribute->getCode();
        $elementType    = $attribute->getDataInput();
        
        $elementConfig  = array();
        $elementConfig['name'] = 'attribute['.$attribute->getId().']';
        $elementConfig['label']= $attribute->getCode();
        $elementConfig['id']   = $attribute->getCode();
        $elementConfig['value']= '';
        $elementConfig['title']= $attribute->getCode();
        $elementConfig['validation']= '';
        $elementConfig['ext_type']  = 'TextField';
        
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
        
        // TODO:
        // Parse option values
        if (isset($this->_dataSources[$attribute->getDataSource()])) {
            $dataSource = (array) $this->_dataSources[$attribute->getDataSource()];
            $elementConfig['ext_type']  = 'ComboBox';
            
            $elementConfig['values'] = $attribute->getOptions()->getHtmlOptions();
        }
                
        $this->addField($elementId, $elementType, $elementConfig);
    }
}