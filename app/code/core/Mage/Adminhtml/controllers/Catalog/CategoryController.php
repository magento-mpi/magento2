<?php
/**
 * Catalog category controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Catalog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('edit');
    }
    
    public function addAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/categories');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);
        
        Mage::register('category', Mage::getModel('catalog/category'));
        if ($id = (int) $this->getRequest()->getParam('id')) {
            Mage::registry('category')->load($id);
        }
        
        //$this->_addBreadcrumb(__('Catalog'), __('Catalog Title'));
        $this->_addBreadcrumb(__('Manage Catalog Categories'), __('Manage Categories Title'));
        
        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
        );
        
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/catalog_category_edit')
        );
        
        $this->renderLayout();
    }
    
    public function jsonTreeAction()
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        $parentNodeId = (int) $this->getRequest()->getPost('node',1);
        $storeId = (int) $this->getRequest()->getPost('store',1);
        
        $tree->getCategoryCollection()->addAttributeToSelect('name');
        $nodes = $tree->load($parentNodeId)
                    ->getNodes();

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
}
