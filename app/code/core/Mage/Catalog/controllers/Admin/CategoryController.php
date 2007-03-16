<?php
/**
 * Admin category controller
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_CategoryController extends Mage_Core_Controller_Admin_Action
{
    /**
     * New category layout
     *
     */
    public function newAction()
    {
        $form = Mage::createBlock('catalog_category_form', 'category_form');
        $this->getResponse()->setBody($form->toString());
    }
    
    public function saveAction()
    {
        echo 'C save';
    }

    public function removeAction() {
        $obj = Mage::getModel('catalog', 'category_tree')->getObject();
        if (intval($_GET['id'])) {
            $obj->removeNode($_GET['id']);
        }
    }

    public function moveAction() {
        $obj = Mage::getModel('catalog', 'category_tree')->getObject();
        if (intval($_POST['id']) && intval($_POST['pid'])) {
            $obj->moveNode($_POST['id'], $_POST['pid']);
        }
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id', null);
        if (!empty($id)) {
            Mage::getModel('catalog', 'category_tree')->deleteNode($id);
        }
    }
    
    /**
     * Categories tree
     *
     */
    public function treeAction()
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/catalogTree.json', 'mage_catalog');
    }
    
    /**
     * Category tree json
     *
     */
    public function treeChildrenAction()
    {
        $tree = Mage::getModel('catalog','category_tree');

        $children = $tree->getLevel($this->getRequest()->getPost('node',1));
        //$data = $tree->getLevel(1);

        $items = array();
        foreach ($children as $child) {
            $item = array();
            $item['text']= $child->getData('attribute_value').'(id #'.$child->getId().')';
            $item['id']  = $child->getId();
            $item['cls'] = 'folder';
            if (!$child->isParent()) {
                $item['leaf'] = 'true';    
            }
            $items[] = $item;
        }

        $this->getResponse()->setBody(Zend_Json::encode($items));        
    }

    //Category attributes
    public function attributesSetGridDataAction()
    {
        /*$block = Mage::createBlock('tpl', 'category_attributes_grid');
        $block->setViewName('Mage_Catalog', 'Admin/category/attributes_set_grid');
        $this->getResponse()->setBody($block->toString());*/
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
