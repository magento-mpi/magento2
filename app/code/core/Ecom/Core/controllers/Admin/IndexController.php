<?php

include_once "Ecom/Core/Controller/Zend/Admin/Action.php";

class IndexController extends Ecom_Core_Controller_Zend_Admin_Action
{
    function indexAction()
    {
        $this->getResponse()->setBody($this->_view->render('layout.php'));
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
						url: '".Ecom::getBaseUrl()."/ecom_catalog/tree/index/',
						loadOnce : false
					}
			},
            'customers_orders':{
					id:'modules:panel:customers_orders', 
					name: 'Customers and Orders', 
					desc: 'Manage Customers adn Orders.',
 					panel: {
						title: 'Customers and Orders',
						url: '".Ecom::getBaseUrl()."/ecom_customer/tree/index/',
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
						url: '".Ecom::getBaseUrl()."/block/loadtree/',
						loadOnce : false
					}
	    	}
		}";
		 
        $this->getResponse()->setBody($data);
    }
}