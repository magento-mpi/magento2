<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin category controller
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author     Dmitriy Soroka <dmitriy@varien.com>
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

        $category = Mage::getModel('catalog/category');
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
            $tree = Mage::getResourceModel('catalog/category_tree');
            $node = Mage::getResourceModel('catalog/category_tree')->loadNode($categoryId);
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
            $tree = Mage::getResourceModel('catalog/category_tree');
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
        $tree = Mage::getResourceModel('catalog/category_tree');
        $parentNodeId = (int) $this->getRequest()->getPost('node',1);
        $storeId = (int) $this->getRequest()->getPost('store',1);

        $nodes = $tree->setStoreId($storeId)
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
    
    public function treeStoreAction()
    {
            $storeId = (int) $this->getRequest()->getParam('store', false);
            if ($storeId) {
                $store = Mage::getModel('core/store')->load($storeId);
            }
            else {
                $store = Mage::getModel('core/store')->setRootCategoryId(1);
            }
            
            $item = array(
                'text'  => __('Catalog Categories'),
                'id'    => $store->getRootCategoryId(),
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
