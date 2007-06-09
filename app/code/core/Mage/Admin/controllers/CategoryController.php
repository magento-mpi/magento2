<?php
/**
 * Admin category controller
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Admin_CategoryController extends Mage_Core_Controller_Front_Action
{
    /**
     * New category layout
     *
     */
    public function newAction()
    {
        $form = $this->getLayout()->createBlock('admin/catalog_category_form', 'category_form');
        $this->getResponse()->setBody($form->toHtml());
    }
    
    public function formAction()
    {
        $form = $this->getLayout()->createBlock('admin/catalog_category_form', 'category_form_json');

        $tabConfig = Array(
            "panelConfig" => Array(
                        "name" => "General",
                        "title" => $form->getTitle(),
                        "type" => "form",
                        "form" => $form->toArray()
                    )
            );
        $this->getResponse()->setBody(Zend_json::encode($tabConfig));
    }
    
    public function saveAction()
    {
        $res = array();

        $parent_id = intval( $this->getRequest()->getPost('parent_category_id') );

        if( $parent_id > 0 ) {
            $category_id = null;
        } else {
            $category_id = intval( $this->getRequest()->getPost('category_id') );
        }

        $category = Mage::getModel('catalog', 'category');
        $category->setAttributeSetId($this->getRequest()->getPost('attribute_set_id'));
        $category->setAttributes($this->getRequest()->getPost('attribute'));
        $category->setCategoryId($category_id);
        $category->setParentId($parent_id);

        try {
            $category->save();
            $res['success'] = true;
            $res['categoryId']   = $category->getId();
            $res['categoryName'] = $category->getName();
        }
        catch (Exception $e){
            $res['errors'] = Array($e->getMessage());
            $res['errorMessage'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }

    public function removeAction() {
        $categoryId = $this->getRequest()->getParam('id',false);
        if ($categoryId) {
            $tree = Mage::getModel('catalog_resource', 'category_tree');
            $node = Mage::getModel('catalog_resource', 'category_tree')->loadNode($categoryId);
            try {
                $tree->removeNode($node);
            }
            catch (Exception $e){
                
            }
        }
    }

    public function moveAction() {
        $nodeId = $this->getRequest()->getPost('id', false);
        $parentNodeId = $this->getRequest()->getPost('pid', false);
        $prevNodeId = $this->getRequest()->getPost('aid', false);
        
        try {
            $tree = Mage::getModel('catalog_resource', 'category_tree');
            $node = $tree->loadNode($nodeId);
            $parentNode = $tree->loadNode($parentNodeId)->loadChildren();
            $prevNode = $tree->loadNode($prevNodeId);
            if ($prevNode->isEmpty()) {
                $prevNode = $parentNode->getLastChild();
            }
            
            $tree->moveNodeTo($node, $parentNode, $prevNode);
        }
        catch (Exception $e){
            
        }
    }

    /**
     * Category tree json
     *
     */
    public function treeChildrenAction()
    {
        $tree = Mage::getModel('catalog_resource','category_tree');
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
    
    public function treeWebsiteAction()
    {
            $websiteId = (int) $this->getRequest()->getParam('website', false);
            if ($websiteId) {
                $website = Mage::getModel('core', 'website')->load($websiteId);
            }
            else {
                $website = Mage::getModel('core', 'website')->setRootCategoryId(1);
            }
            
            $item = array(
                'text'  => __('Catalog categories'),
                'id'    => $website->getRootCategoryId(),
                'cls'   => 'folder',
                'isRoot'=> 'true',
                'expanded' => 'true'
            );
            $this->getResponse()->setBody(Zend_Json::encode(array($item)));
    }

    //Category attributes
    public function attributesSetGridDataAction()
    {
    }
    
    public function attributesGridAction()
    {
        echo 'atttibutes';
    }
    
    public function arrtibutesSetTreeAction()
    {
        echo 'tree';
    }
}
