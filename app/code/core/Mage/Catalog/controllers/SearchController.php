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
            Mage::getBlock('search.form.mini')->assign('query', $searchQuery);
            $searchResBlock = Mage::createBlock('catalog_search_result', 'search.result', array('query'=>$searchQuery));
            $searchResBlock->loadByQuery($this->getRequest());
            
            Mage::getBlock('content')->append($searchResBlock);
        }
        else {
            
        }
        
        $this->renderLayout();
    }
    
    public function byAction()
    {
        $this->loadLayout('front');
        
        $attribute = $this->getRequest()->getParam('attr', false);
        $value = $this->getRequest()->getParam('value', false);
        if (!$attribute || !$value) {
            $this->_forward('noroute');
            return;
            //$this->_redirect('noroute');
        }
        
        // check if attr exist
        $arrOptionId = Mage::getModel('catalog','product_attribute')
            ->loadByCode($attribute)
            ->getOptions()
                ->getArrItemId();

        if (empty($arrOptionId) || !in_array($value, $arrOptionId)) {
            $this->_forward('noroute');
            return;
            //$this->_redirect('noroute');
        }
        
        Mage::getBlock('catalog.leftnav')->assign($attribute, $value);
        
        $block = Mage::createBlock('catalog_search_result', 'search.byattribute');
        $block->loadByAttributeOption($this->getRequest());
        
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function advancedAction()
    {
        $this->loadLayout('front');
        $block = Mage::createBlock('tpl', 'catalog.search.advanced')
            ->setTemplate('catalog/search/form.advanced.phtml');
            //->assign('messages',    Mage::getSingleton('customer', 'session')->getMessages(true))
            //->assign('customer', Mage::getSingleton('customer', 'session')->getCustomer());
        Mage::getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    public function advancedResultAction()
    {
    }
}