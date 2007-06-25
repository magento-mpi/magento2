<?php
/**
 * Catalog admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Adminhtml_CustomerPageController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('catalog');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('catalog'), __('catalog title'));
        $this->renderLayout();
    }
    
    public function customerpageAction() {    	
    	$grid = $this->getLayout()->createBlock('adminhtml/customerpage');
        $page = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/page.phtml');
        $page->setChild('content', $grid);        
        
        $this->getResponse()->setBody($page->toHtml());   
    }
    
    public function doSearchCustomersAction() { 
    	$collection = Mage::getResourceModel('customer/customer_collection');        
        $collection = $collection->addNameFilter($this->getRequest()->getParam('uname', false));
          	
        $this->loadLayout('baseframe');
        $customers = $collection->loadData()->getItems();
        $tags = Mage::getModel('catalog/tags');
        $c = array();
        foreach ($customers as $customer) {
			$data = $customer->getData();
			$u_tags = $tags->getUserTags($data['customer_id'], 2);
			$data['products'] = $tags->getTaggedProducts($data['customer_id']);
			$data['tags'] = $u_tags;
			$c[] = $data;
		}		
        
        $grid = $this->getLayout()->createBlock('adminhtml/customerpage')
        	->assign('customers', $c);;
        $page = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/page.phtml');
        $page->setChild('content', $grid);        
        
        $this->getResponse()->setBody($page->toHtml()); 
    }
}
