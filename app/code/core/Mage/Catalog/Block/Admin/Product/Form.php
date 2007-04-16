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
    protected $_group;
    protected $_dataInputs;
    protected $_dataSources;
    
    public function __construct() 
    {
        parent::__construct();
        // Config settings
        $this->_dataInputs = (array) Mage::getConfig()->getXml()->admin->dataInputs;
        $this->_dataSources= (array) Mage::getConfig()->getXml()->modules->Mage_Catalog->admin->dataSources;

        $this->setViewName('Mage_Core', 'form.phtml');
        
        // Set form attributes
        $this->setAttribute('method', 'POST');
        $this->setAttribute('class', 'x-form');
        $this->setAttribute('action', Mage::getBaseUrl().'/mage_catalog/product/save/');
        
        // Request params
        $groupId  = Mage::registry('controller')->getRequest()->getParam('group', false);
        $setId    = Mage::registry('controller')->getRequest()->getParam('set', false);
        $productId= (int) Mage::registry('controller')->getRequest()->getParam('product', false);
        $isDefault= (bool) Mage::registry('controller')->getRequest()->getParam('isdefault', false);
        
        if ($groupId) {
            $this->_group = Mage::getModel('catalog_resource', 'product_attribute_group')->get($groupId);
            if ($this->_group) {
                $this->setAttribute('legend', $this->_group['product_attribute_group_code']);
                $this->setAttribute('id', 'form_'.$groupId);
            }
        }
        
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
                    'name'  => 'attribute_set_id',
                    'value' => $setId,
                    'id'    => 'attribute_set_id'
                )
            );
        }
        
        $attributes = Mage::getModel('catalog_resource', 'product_attribute_group')->getAttributes($groupId, $setId);
        foreach ($attributes as $attribute) {
            $this->attribute2field($attribute);
        }
        
        if ($productId) {
            $product = Mage::getModel('catalog_resource','product')->load($productId);
            $productInfo = $product->getData();
            $this->setElementsValues($productInfo);
        }
    }
    
    public function attribute2field($attribute)
    {
        $elementId      = $attribute['attribute_code'];
        $elementType    = $attribute['data_input'];
        
        $elementConfig  = array();
        $elementConfig['name'] = 'attribute['.$attribute['attribute_id'].']';
        $elementConfig['label']= $attribute['attribute_code'];
        $elementConfig['id']   = $attribute['attribute_code'];
        $elementConfig['value']= '';
        $elementConfig['title']= $attribute['attribute_code'];
        $elementConfig['validation']= '';
        $elementConfig['ext_type']  = 'TextField';
        
        // Parse input element params
        if (isset($this->_dataInputs[$attribute['data_input']])) {
            $htmlParams = (array) $this->_dataInputs[$attribute['data_input']];
            $htmlParams = isset($htmlParams['htmlParams']) ? (array) $htmlParams['htmlParams'] : array();
            foreach ($htmlParams as $paramName=>$paramValue) {
                if (!isset($elementConfig[$paramName])) {
                    $elementConfig[$paramName] = $paramValue;
                }
            }
        }
        
        // Parse option values
        if (isset($this->_dataSources[$attribute['data_source']])) {
            $dataSource = (array) $this->_dataSources[$attribute['data_source']];
            $elementConfig['ext_type']  = 'ComboBox';
            $elementConfig['values'] = Mage::getModel('catalog_resource', $dataSource['model'])->$dataSource['method']((array) $dataSource['params']);
        }
                
        $this->addField($elementId, $elementType, $elementConfig);
    }
}