<?php
/**
 * Catalog Search Controller
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_CatalogSearch_ResultController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
    {
        $this->loadLayout();
            
        $searchQuery = $this->getRequest()->getParam('q', false);
        if ($searchQuery) {
            $this->getLayout()->getBlock('top.search')->assign('query', $searchQuery);
            $searchResBlock = $this->getLayout()->createBlock('catalogsearch/search', 'search.result', array('query'=>$searchQuery));
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
        
        $block = $this->getLayout()->createBlock('catalogsearch/search', 'search.byattribute');
        $block->loadByAttributeOption($this->getRequest());
        
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
}
