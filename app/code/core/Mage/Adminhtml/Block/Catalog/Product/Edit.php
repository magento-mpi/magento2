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
        $this->setTemplate('catalog/product/edit.phtml');
        $this->setId('product_edit');
    }
    
    protected function _initChildren()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/').'\')',
                    'class' => 'back'
                ))
        );

        $this->setChild('cancel_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Product'),
                    'onclick'   => 'productForm.submit()',
                    'class' => 'save'
                ))
        );
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete Product'),
                    'onclick'   => 'customerDelete()',
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
        return $this->getChildHtml('cancel_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }
    
    public function getProductId()
    {
        return Mage::registry('product')->getId();
    }
    
    public function getRelatedProductsJSON()
    {
    	$result = array();
    	
        foreach (Mage::registry('product')->getRelatedProducts() as $product) {
        	$result[$product->getEntityId()] = $product->toArray(
        		$product->getAttributeCollection()->getColumnValues('product_link_attribute_code')
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
    	
        foreach (Mage::registry('product')->getUpSellProducts() as $product) {
        	$result[$product->getEntityId()] = $product->toArray(
        		$product->getAttributeCollection()->getColumnValues('product_link_attribute_code')
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
    	
        foreach (Mage::registry('product')->getCrossSellProducts() as $product) {
        	$result[$product->getEntityId()] = $product->toArray(
        		$product->getAttributeCollection()->getColumnValues('product_link_attribute_code')
        	);
        }
        
        if(!empty($result)) {
        	return Zend_Json_Encoder::encode($result);
        }
        
        return '{}';
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
    
}
