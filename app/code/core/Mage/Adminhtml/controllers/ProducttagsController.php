<?php
/**
 * Catalog admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Adminhtml_ProductTagsController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction() {	 	
    	
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('catalog');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('catalog'), __('catalog title'));
            
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/catalog_producttags'));
        $this->renderLayout();
    }
    
    public function tagSetStatusPostAction() {
    	$tags = $this->getRequest()->getParam('tags', false);
    	$tag_model = Mage::getSingleton('catalog/tags');
    	$tag_model->setStatus($tags);
    	
    	$this->producttagsAction();
    }
    
	public function producttagsAction()
    {          
        $grid = $this->getLayout()->createBlock('adminhtml/producttags');
        $page = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/page.phtml');
        $page->setChild('content', $grid);        
        
        $this->getResponse()->setBody($page->toHtml());     
        
    }
        
    public function framesetAction()
    {
        $frameset = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/tags/frameset.phtml');
        $this->getResponse()->setBody($frameset->toHtml());
    }
    
    public function menuAction()
    {
        $treeBlock = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/tags/menu.phtml');
        $this->getResponse()->setBody($treeBlock->toHtml());
    }
    
    public function categoryTreeDataAction()
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        $parentNodeId = (int) $this->getRequest()->getPost('node',1);
        $websiteId = (int) $this->getRequest()->getPost('website',1);

        $nodes = $tree->setWebsiteId($websiteId)
                    ->joinAttribute('name')
                    ->loadNode($parentNodeId)
                        ->loadChildren(1)
                        ->getChildren();

        $items = array();
        foreach ($nodes as $node) {
            $item = array();
            $item['text']= $node->getName(); //.'(id #'.$child->getId().')';
            $item['id']  = $node->getId();
            $item['cls'] = 'folder';
            $item['allowDrop'] = true;
            $item['allowDrag'] = true;
            if (!$node->hasChildren()) {
                $item['leaf'] = 'true';    
            }
            $items[] = $item;
        }

        $this->getResponse()->setBody(Zend_Json::encode($items));
    }
    
    public function productGridAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/catalog/producttags');
        $grid->setCategoryId($this->getRequest()->getParam('categoryId', 1));

        $page = $this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/catalog/page.phtml');
        $page->setChild('content', $grid);
        
        $this->getResponse()->setBody($page->toHtml());
    }
}
