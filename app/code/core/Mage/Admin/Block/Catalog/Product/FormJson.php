<?php
/**
 * Product attributes form
 *
 * @package    Mage
 * @subpackage Admin
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Admin_Block_Catalog_Product_FormJson extends Varien_Data_Form
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
        $productId= (int) Mage::registry('controller')->getRequest()->getParam('product', false);

        // Config settings
        $this->_dataInputs = (array) Mage::getConfig()->getNode('admin/dataInputs');

        // Set form attributes
        $postUrl = Mage::getUrl('admin', array('controller'=>'product', 'action'=>'save'));
        if ($productId) {
            $postUrl.= 'product/'.$productId;
        }
        
        $this->setMethod('POST');
        $this->setAction($postUrl);
        //$this->setFileupload(false);
        
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