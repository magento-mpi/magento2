<?php
/**
 * Product attribute add/edit form options tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/attribute/options.phtml');
    }
    
	protected function _initChildren()
	{
		$this->setChild('delete_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label' => __('Delete'),
                    'class' => 'delete delete-option'
				)));
				
		$this->setChild('add_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label' => __('Add Option'),
                    'class' => 'add',
                    'id'    => 'add_new_option_button'
				)));
	}
	
	public function getDeleteButtonHtml()
	{
	    return $this->getChildHtml('delete_button');
	}
    
	public function getAddNewButtonHtml()
	{
	    return $this->getChildHtml('add_button');
	}

	public function getStores()
	{
	    $stores = $this->getData('stores');
	    if (is_null($stores)) {
            $stores = Mage::getModel('core/store')
    	        ->getResourceCollection()
    	        ->setLoadDefault(true)
    	        ->load();
            $this->setData('stores', $stores);
	    }
	    return $stores;
	}
	
	public function getOptionValues()
	{
	    $values = $this->getData('option_values');
	    if (is_null($values)) {
	        $values = array();
	        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setPositionOrder('desc')
                ->load();
                
            foreach ($optionCollection as $option) {
                $value = array();
            	$value['id'] = $option->getId();
            	$value['sort_order'] = $option->getSortOrder();
            	foreach ($this->getStores() as $store) {
            		$storeValues = $this->getStoreOptionValues($store->getId());
            		if (isset($storeValues[$option->getId()])) {
            		    $value['store'.$store->getId()] = htmlspecialchars($storeValues[$option->getId()]);
            		}
            		else {
            		    $value['store'.$store->getId()] = '';
            		}
            	}
            	$values[] = new Varien_Object($value);
            }
            $this->setData('option_values', $values);
	    }
	    
	    return $values;
	}
	
	public function getLabelValues()
	{
	    $values = array();
	    $values[0] = $this->getAttributeObject()->getFrontend()->getLabel();
	    $translations = Mage::getModel('core/translate_string')
	       ->load($this->getAttributeObject()->getFrontend()->getLabel())
	       ->getStoreTranslations();
	    foreach ($this->getStores() as $store) {
	        if ($store->getId() != 0) {
	            $values[$store->getId()] = isset($translations[$store->getId()]) ? $translations[$store->getId()] : '';
	        }
	    }
	    return $values;
	}
	
	public function getStoreOptionValues($storeId)
	{
        $values = $this->getData('store_option_values_'.$storeId);
        if (is_null($values)) {
            $values = array();
            $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setStoreFilter($storeId, false)
                ->load();
            foreach ($valuesCollection as $item) {
            	$values[$item->getId()] = $item->getValue();
            }
            $this->setData('store_option_values_'.$storeId, $values);
        }
        return $values;
	}
	
	public function getAttributeObject()
	{
	    return Mage::registry('entity_attribute');
	}
}