<?php
/**
 * Catalog admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Adminhtml_ProductPageController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('catalog');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('catalog'), __('catalog title'));
        $this->renderLayout();
    }
    
    public function ProductPageAction() {    	
    	$grid = $this->getLayout()->createBlock('adminhtml/productpage');
        $page = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/page.phtml');
        $page->setChild('content', $grid);        
        
        $this->getResponse()->setBody($page->toHtml());   
    }
    
    public function doSearchProductsAction() { 
    	$this->loadLayout('baseframe');
    	
    	$collection = Mage::getResourceModel('catalog/product_collection');
        $collection = $collection->addSearchFilter($this->getRequest()->getParam('pname', false));        
        
        
        $products = $collection->loadData()->getItems();
        
        $tags = Mage::getModel('catalog/tags');
        $c = array();
        foreach ($products as $product) {
			$data = $product->getData();			
			$u_tags = $tags->getProductTags($data['product_id'], 2);			
			$data['customers'] = $tags->getTaggedCustomers($data['product_id']);
			$data['tags'] = $u_tags;
			$c[] = $data;
		}
        
		$grid = $this->getLayout()->createBlock('adminhtml/productpage')
			->assign('products', $c);;
        $page = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/page.phtml');
        $page->setChild('content', $grid);        
        
        $this->getResponse()->setBody($page->toHtml());         
    }
}
