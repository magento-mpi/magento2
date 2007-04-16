<?php
/**
 * Catalog Search Controller
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_SearchController extends Mage_Core_Controller_Front_Action
{
    public function resultAction() 
    {
        $searchQuery = $this->getRequest()->getParam('q', false);
        if ($searchQuery) {
            Mage::getBlock('search.form.mini')->assign('query', $searchQuery);
            $searchResBlock = Mage::createBlock('catalog_search_result', 'search.result', array('query'=>$searchQuery));
            $searchResBlock->loadByQuery($this->getRequest());
            
            Mage::getBlock('content')->append($searchResBlock);
        }
        else {
            
        }
    }
    
    public function byAction()
    {
        $attribute = $this->getRequest()->getParam('attr', false);
        $value = $this->getRequest()->getParam('value', false);
        if (!$attribute || !$value) {
            //$this->_forward('noroute');
            $this->_redirect('noroute');
        }
        
        // check if attr exist
        $attributes = Mage::getModel('catalog_resource','product_attribute_option')->getOptionsId(array('option_type'=>$attribute));

        if (empty($attributes) || !is_array($attributes) || !in_array($value, $attributes)) {
            $this->_redirect('noroute');
        }
        
        Mage::getBlock('catalog.leftnav')->assign($attribute, $value);
        
        $block = Mage::createBlock('catalog_search_result', 'search.byattribute');
        $block->loadByAttribute($this->getRequest());
        
        Mage::getBlock('content')->append($block);
        
    }
}