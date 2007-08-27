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
        $searchQuery = $this->getRequest()->getParam('q', false);

        if ($searchQuery) {
        	$search = Mage::getModel('catalogsearch/search')->loadByQuery($searchQuery);
        	if (!$search->getId()) {
        		
        		$search->setSearchQuery($searchQuery)->updateSearch();
        		
        	} elseif ($search->getRedirect()) {
        		
	    		$search->updateSearch();
        		$this->getResponse()->setRedirect($search->getRedirect());
        		return;
        		
        	} elseif ($search->getSynonimFor()) {
        		
        		$search->updateSearch();
        		$searchQuery = $search->getSynonimFor();
        		
        	}
        }
        
        $this->loadLayout();
            

        $this->getLayout()->getBlock('top.search')->assign('query', $searchQuery);
        $searchResBlock = $this->getLayout()->createBlock('catalogsearch/result', 'search.result', array('query'=>$searchQuery));
        //$searchResBlock->loadByQuery($this->getRequest());

        $this->getLayout()->getBlock('content')->append($searchResBlock);
        
        $this->renderLayout();
    }
}
