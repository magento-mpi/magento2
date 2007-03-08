<?php

class IndexController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        #echo "<pre>"; print_r(Mage_Core_Block::getAllBlocks());
        #Mage_Core_Block::loadJsonFile('Mage/Core/Admin/initLayout.json');
        #$this->getResponse()->setBody($this->_view->render('layout2.php'));
        
    }
    
    function treeSwitchAction()
    {
		 $data = 
		 "{
        	'catalog': {
					id:'modules:panel:catalog', 
					name: 'Catalog', 
					desc: 'Manage Categories and Products.', 
					panel: {
						title: 'Catalog',
						url: '".Mage::getBaseUrl()."/mage_catalog/tree/index/',
						loadOnce : false
					}
			},
            'customers_orders':{
					id:'modules:panel:customers_orders', 
					name: 'Customers and Orders', 
					desc: 'Manage Customers adn Orders.',
 					panel: {
						title: 'Customers and Orders',
						url: '".Mage::getBaseUrl()."/mage_customer/tree/index/',
						loadOnce : false
					}
			},
            'modules':{
					id:'modules:panel:modules', 
					name: 'Modules', 
					desc: 'Setup and Configuration of modules.', 
 					panel: {
						title: 'Modules'
					}
			},
            'blocks':{
					id:'modules:panel:blocks', 
					name: 'Blocks', 
					desc: 'Setup and Edit layout blocks.', 
 					panel: {
						title: 'Blocks',
						url: '".Mage::getBaseUrl()."/block/loadtree/',
						loadOnce : false
					}
	    	}
		}";
		 
        $this->getResponse()->setBody($data);
    }
}