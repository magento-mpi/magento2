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
        $this->loadLayout();
            
        $searchQuery = $this->getRequest()->getParam('q', false);
        if ($searchQuery) {
            $this->getLayout()->getBlock('search.form.mini')->assign('query', $searchQuery);
            $searchResBlock = $this->getLayout()->createBlock('catalog/product_search', 'search.result', array('query'=>$searchQuery));
            $searchResBlock->loadByQuery($this->getRequest());
            
            $this->getLayout()->getBlock('content')->append($searchResBlock);
        }
        else {
            
        }
        
        $this->renderLayout();
    }
    
    public function byAction()
    {
        $this->loadLayout();
        
        $attribute = $this->getRequest()->getParam('attr', false);
        $value = $this->getRequest()->getParam('value', false);
        if (!$attribute || !$value) {
            $this->_forward('noroute');
            return;
            //$this->getResponse()->setRedirect('noroute');
        }
        
        // check if attr exist
        $arrOptionId = Mage::getModel('catalog/product_attribute')
            ->loadByCode($attribute)
            ->getOptions()
                ->getArrItemId();

        if (empty($arrOptionId) || !in_array($value, $arrOptionId)) {
            $this->_forward('noroute');
            return;
            //$this->getResponse()->setRedirect('noroute');
        }
        
        $this->getLayout()->getBlock('catalog.leftnav')->assign($attribute, $value);
        
        $block = $this->getLayout()->createBlock('catalog/product_search', 'search.byattribute');
        $block->loadByAttributeOption($this->getRequest());
        
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function advancedAction()
    {
        $this->loadLayout();
        
        $categories = Mage::getResourceModel('catalog/category_tree')
            ->joinAttribute('name')
            ->load(1) // TODO: from config
            ->getNodes();
        $types = Mage::getModel('catalog/product_attribute')
            ->loadByCode('type')
            ->getSource()
                ->getArrOptions();
        $manufacturers = Mage::getModel('catalog/product_attribute')
            ->loadByCode('manufacturer')
            ->getSource()
                ->getArrOptions();
        
        $block = $this->getLayout()->createBlock('core/template', 'catalog.search.advanced')
            ->setTemplate('catalog/search/form.advanced.phtml')
            ->assign('action', Mage::getUrl('catalog/search/advancedResult'))
            ->assign('categories', $categories)
            ->assign('types', $types)
            ->assign('manufacturers', $manufacturers);

        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    public function advancedResultAction()
    {
        $this->loadLayout();
            
        $block = $this->getLayout()->createBlock('catalog/product_search', 'search.result');
        $block->loadByAdvancedSearch($this->getRequest());
        
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
   }
}