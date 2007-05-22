<?php
/**
 * Product attributes form
 *
 * @package    Mage_Admin
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Catalog_Block_Admin_Product_Form extends Mage_Core_Block_Form
{
    /**
     * Data inputs configuration
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_dataInputs;
    
    /**
     * Data sources configuration
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_dataSources;

    /**
     * Constructor
     *
     */
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

        $this->setTemplate('form.phtml');
        
        // Set form attributes
        $postUrl = Mage::getBaseUrl().'mage_catalog/product/save/';
        if ($productId) {
            $postUrl.= 'product/'.$productId;
        }
        $this->setAttribute('method', 'POST');
        $this->setAttribute('class', 'x-form');
        $this->setAttribute('action', $postUrl);
        

        $group = Mage::getModel('catalog', 'product_attribute_group')->load($groupId);
        $this->setAttribute('legend', $group->getCode());
        $this->setAttribute('id', 'form_'.$groupId);
        
        if ($setId) {
            $this->addField('set_id', 'hidden', array('name'=>'set_id', 'value'=>$setId));
        }
        
        $attributes = $group->getAttributes();
        foreach ($attributes as $attribute) {
            $this->attribute2field($attribute);
        }
        
        if ($productId) {
            $product = Mage::getModel('catalog','product')->load($productId);
            $productInfo = $product->getData();
            $this->setElementsValues($productInfo);
        }
    }
    
    /**
     * Convert attribute object to field description
     *
     * @param Mage_Catalog_Model_Product_Attribute $attribute
     * @return Mage_Catalog_Block_Admin_Product_Form
     */
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
        if ($source = $attribute->getSource()) {
            //$dataSource = (array) $this->_dataSources[$attribute->getDataSource()];
            $elementConfig['ext_type']  = 'ComboBox';
            $elementConfig['values'] = $source->getArrOptions();
        }
                
        $this->addField($elementId, $elementType, $elementConfig);
        
        return $this;
    }
}