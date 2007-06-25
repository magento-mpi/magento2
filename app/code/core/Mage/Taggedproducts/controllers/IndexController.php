<?php
/**
 * Page Index Controller
 *
 * @copyright  Varien, 2007
 */

class Mage_Taggedproducts_IndexController extends Mage_Core_Controller_Front_Action {
    function searchAction() {
        $this->loadLayout();

        $searchQuery = $this->getRequest()->getParam('q', false);
        
        $searchResBlock = $this->getLayout()->createBlock('taggedproducts/results');
        $searchResBlock->getResults($this->getRequest());

        $this->getLayout()->getBlock('content')->append($searchResBlock);
        
        $this->renderLayout();
    }
    
    function indexAction() {
        $this->searchAction();
    }
    
    function xmlAction() {
    	echo "1";
    }
}