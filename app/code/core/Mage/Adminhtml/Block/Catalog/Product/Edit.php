<?php
/**
 * Customer edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit.phtml');
        $this->setId('product_edit');
    }
    
    protected function _initChildren()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                    'class' => 'back'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save'),
                    'onclick'   => 'productForm.submit()',
                    'class' => 'save'
                ))
        );

        
        $this->setChild('save_and_edit_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save And Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit()',
                    'class' => 'save'
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete'),
                    'onclick'   => 'confirmSetLocation(\''.__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                    'class'  => 'delete'
                ))
        );
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndEditButtonHtml()
    {
        return $this->getChildHtml('save_and_edit_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }
    
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }
    
    public function getProductId()
    {
        return Mage::registry('product')->getId();
    }
    
    public function getProductSetId()
    {
        $setId = false;
        if (!($setId = Mage::registry('product')->getAttributeSetId()) && $this->getRequest()) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        return $setId;
    }
    
    public function getRelatedProductsJSON()
    {
    	$result = array();
    	
        foreach (Mage::registry('product')->getRelatedProductsLoaded() as $product) {
        	$result[$product->getEntityId()] = $product->toArray(
        		$product->getAttributeCollection()->getAttributeCodes()
        	);
        }
        
        if(!empty($result)) {
        	return Zend_Json_Encoder::encode($result);
        }
        
        return '{}';
    }
    
    
    public function getUpSellProductsJSON()
    {
    	$result = array();
    	
        foreach (Mage::registry('product')->getUpSellProductsLoaded() as $product) {
        	$result[$product->getEntityId()] = $product->toArray(
        		$product->getAttributeCollection()->getAttributeCodes()
        	);
        }
        
        if(!empty($result)) {
        	return Zend_Json_Encoder::encode($result);
        }
        
        return '{}';
    }

    public function getCrossSellProductsJSON()
    {
    	$result = array();
    	
        foreach (Mage::registry('product')->getCrossSellProductsLoaded() as $product) {
        	$result[$product->getEntityId()] = $product->toArray(
        		$product->getAttributeCollection()->getAttributeCodes()
        	);
        }
        
        if(!empty($result)) {
        	return Zend_Json_Encoder::encode($result);
        }
        
        return '{}';
    }
    
    public function getSuperGroupProductJSON()
    {
    	$result = array();
    	
        foreach (Mage::registry('product')->getSuperGroupProductsLoaded() as $product) {
        	$result[$product->getEntityId()] = $product->toArray(
        		$product->getAttributeCollection()->getAttributeCodes()
        	);
        }
        
        if(!empty($result)) {
        	return Zend_Json_Encoder::encode($result);
        }
        
        return '{}';
    }
    
    
    public function getIsSuperGroup() 
    {
    	return Mage::registry('product')->isSuperGroup();
    }
    
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }
    
    public function getHeader()
    {
        if (Mage::registry('product')->getId()) {
            return Mage::registry('product')->getName();
        }
        else {
            return __('New Product');
        }
    }
    
    public function getIsConfigured()
    {
    	if (!($superAttributes = Mage::registry('product')->getSuperAttributesIds())) {
            $superAttributes = false;
        }
                
    	return !Mage::registry('product')->isSuperConfig() || $superAttributes !== false;
    }
    
}
